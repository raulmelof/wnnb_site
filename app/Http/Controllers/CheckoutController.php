<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;

class CheckoutController extends Controller
{
    /**
     * PASSO 2 (Doc InfinitePay): Criar o Pedido localmente
     * e redirecionar para o link de pagamento.
     * AGORA INCLUI VERIFICAÇÃO E DECREMENTO DE ESTOQUE.
     */
    public function iniciarPagamento(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $user = Auth::user();
        $freteNome = $request->input('frete_tipo');
        $freteValor = (float) $request->input('frete_valor', 0);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $pedido = null;
        $total = 0;
        $items_para_infinitepay = [];

        try {
            DB::transaction(function () use ($cart, $user, &$pedido, &$total, &$items_para_infinitepay) {
                
                // --- 1. VERIFICAR ESTOQUE (MANTIDO) ---
                foreach ($cart as $variacao_id => $details) {
                    $variacao = ProdutoVariacao::find($variacao_id);
                    if ($details['quantidade'] > $variacao->estoque) {
                        throw new Exception("Estoque insuficiente para o produto {$details['nome']} ({$details['tamanho']}).");
                    }
                }

                // --- 2. CALCULAR TOTAL E MONTAR ITENS DA API ---
                foreach ($cart as $id => $details) {
                    $subtotal = $details['preco'] * $details['quantidade'];
                    $total += $subtotal;

                    $items_para_infinitepay[] = [
                        'name' => $details['nome'] . ' (Tamanho: ' . $details['tamanho'] . ')',
                        'price' => (int) ($details['preco'] * 100), 
                        'quantity' => $details['quantidade'],
                    ];
                }
                
                // Adiciona o frete como um "item" para a InfinitePay
                // Isso garante que o valor total bata com o valor cobrado
                if ($freteValor > 0) {
                    $total += $freteValor; // Soma ao total do banco

                    $items_para_infinitepay[] = [
                        'name' => "Frete: $freteNome",
                        'price' => (int) ($freteValor * 100), // Centavos
                        'quantity' => 1,
                    ];
                }

                // --- 3. CRIAR O PEDIDO ---
                $pedido = Pedido::create([
                    'user_id' => $user->id,
                    'total' => $total,
                    'status' => 'aguardando_pagamento',
                ]);

                // --- 4. ASSOCIAR PRODUTOS (SEM DECREMENTAR ESTOQUE) ---
                foreach ($cart as $variacao_id => $details) {
                    $pedido->produtos()->attach($details['produto_id'], [
                        'produto_variacao_id' => $variacao_id,
                        'quantidade' => $details['quantidade'],
                        'preco' => $details['preco']
                    ]);
                    
                    // A LINHA DE DECREMENT FOI REMOVIDA DAQUI
                }
            });

        } catch (Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        // --- 5. LIMPAR O CARRINHO ---
        $request->session()->forget('cart');

        // --- 6. MONTAR A URL DE PAGAMENTO ---
        $infinitepay_handle = 'guilhermecfrancellino'; 
        $params = [
            'handle' => $infinitepay_handle,
            'items' => json_encode($items_para_infinitepay),
            'order_nsu' => $pedido->id, 
            'redirect_url' => route('checkout.callback'), 
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ];
        $query_string = http_build_query($params);
        $infinitepay_url = "https://checkout.infinitepay.io/{$infinitepay_handle}?{$query_string}";

        // --- 7. REDIRECIONAR O USUÁRIO ---
        return redirect()->away($infinitepay_url);
    }

    /**
     * PASSO 3 e 4 (Doc InfinitePay): Receber o callback,
     * verificar o pagamento e atualizar o pedido.
     */
    public function processarCallback(Request $request)
    {
        $order_nsu = $request->input('order_nsu');
        $transaction_id = $request->input('transaction_id');
        $capture_method = $request->input('capture_method');
        $receipt_url = $request->input('receipt_url');
        $slug = $request->input('slug');

        $pedido = Pedido::find($order_nsu);

        if (!$pedido) {
            return redirect()->route('home')->with('error', 'Pedido não encontrado.');
        }

        // Se o pedido já foi processado (pago ou falhou), apenas redireciona
        if ($pedido->status !== 'aguardando_pagamento') {
             if ($pedido->status === 'pago') {
                return redirect()->route('checkout.sucesso', ['pedido' => $pedido->id]);
             } else {
                return redirect()->route('cart.index')->with('error', 'O pagamento deste pedido falhou ou foi cancelado.');
             }
        }

        $infinitepay_handle = 'guilhermecfrancellino'; 

        try {
            $response = Http::post("https://api.infinitepay.io/invoices/public/checkout/payment_check/{$infinitepay_handle}", [
                'handle' => $infinitepay_handle,
                'transaction_nsu' => $transaction_id,
                'external_order_nsu' => $order_nsu,
                'slug' => $slug,
            ]);

            $data = $response->json();

            if (isset($data['success']) && $data['success'] === true && isset($data['paid']) && $data['paid'] === true) {
                
                // SUCESSO! PAGAMENTO APROVADO
                // AGORA VAMOS VERIFICAR O ESTOQUE E DECREMENTAR
                
                try {
                    DB::transaction(function () use ($pedido, $transaction_id, $capture_method, $receipt_url, $slug) {
                        
                        // Encontra os itens do pedido
                        $itensDoPedido = DB::table('pedido_produtos')
                                           ->where('pedido_id', $pedido->id)
                                           ->get();

                        // 1. CHECA O ESTOQUE NOVAMENTE (Proteção contra race condition)
                        foreach ($itensDoPedido as $item) {
                            $variacao = ProdutoVariacao::find($item->produto_variacao_id);
                            if ($item->quantidade > $variacao->estoque) {
                                throw new Exception("Ocorreu um erro. O produto {$variacao->produto->nome} ({$variacao->tamanho}) não possui mais estoque.");
                            }
                        }

                        // 2. DECREMENTA O ESTOQUE
                        foreach ($itensDoPedido as $item) {
                            $variacao = ProdutoVariacao::find($item->produto_variacao_id);
                            $variacao->decrement('estoque', $item->quantidade);
                        }

                        // 3. ATUALIZA O PEDIDO
                        $pedido->update([
                            'status' => 'pago',
                            'transaction_id' => $transaction_id,
                            'payment_method' => $capture_method,
                            'receipt_url' => $receipt_url,
                            'infinitepay_slug' => $slug,
                        ]);
                    });

                } catch (Exception $e) {
                    // O pagamento foi APROVADO, mas o ESTOQUE FALHOU (Oversell)
                    // Este é um cenário crítico. O pedido é marcado como 'falhou'
                    // e o suporte deve ser notificado para estornar o cliente.
                    $pedido->update(['status' => 'falhou']);
                    // TODO: Notificar o administrador sobre o erro de oversell
                    return redirect()->route('home')->with('error', 'Pagamento aprovado, mas falha ao reservar estoque. Contate o suporte.');
                }

                // Redireciona para o sucesso
                return redirect()->route('checkout.sucesso', ['pedido' => $pedido->id]);

            } else {
                
                // FALHA! O pagamento não foi confirmado.
                // Apenas atualiza o status. Não é necessário restaurar estoque
                // pois ele nunca foi removido.
                $pedido->update(['status' => 'falhou']);
                return redirect()->route('cart.index')->with('error', 'O pagamento falhou ou foi cancelado.');
            }

        } catch (Exception $e) {
            return redirect()->route('home')->with('error', 'Erro ao verificar o pagamento. Contate o suporte.');
        }
    }

    /**
     * Página final de "Obrigado pela compra".
     */
    public function mostrarSucesso(Pedido $pedido)
    {
        if ($pedido->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.sucesso', ['pedido' => $pedido]);
    }
}