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

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $pedido = null;
        $total = 0;
        $items_para_infinitepay = [];

        try {
            // A transação garante que tudo abaixo aconteça, ou nada aconteça.
            DB::transaction(function () use ($cart, $user, &$pedido, &$total, &$items_para_infinitepay) {
                
                // --- 1. VERIFICAR ESTOQUE (NOVO) ---
                foreach ($cart as $variacao_id => $details) {
                    $variacao = ProdutoVariacao::find($variacao_id);
                    if ($details['quantidade'] > $variacao->estoque) {
                        // Se não houver estoque, lança um erro e para a transação
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

                // --- 3. CRIAR O PEDIDO ---
                $pedido = Pedido::create([
                    'user_id' => $user->id,
                    'total' => $total,
                    'status' => 'aguardando_pagamento',
                ]);

                // --- 4. ASSOCIAR PRODUTOS E DECREMENTAR ESTOQUE (MODIFICADO) ---
                foreach ($cart as $variacao_id => $details) {
                    
                    // Associa o pedido à variação na tabela 'pedido_produtos'
                    $pedido->produtos()->attach($details['produto_id'], [
                        'produto_variacao_id' => $variacao_id, // A NOVA COLUNA
                        'quantidade' => $details['quantidade'],
                        'preco' => $details['preco']
                    ]);
                    
                    // Decrementa o estoque (NOVO)
                    $variacao = ProdutoVariacao::find($variacao_id);
                    $variacao->decrement('estoque', $details['quantidade']);
                }
            });

        } catch (Exception $e) {
            // Se algo der errado (especialmente nosso erro de estoque),
            // o usuário é redirecionado com a mensagem de erro.
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        // --- 5. LIMPAR O CARRINHO ---
        $request->session()->forget('cart');

        // --- 6. MONTAR A URL DE PAGAMENTO (Passo 2 da Doc) ---
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

        // Se o pedido já foi pago, apenas redireciona
        if ($pedido->status === 'pago') {
             return redirect()->route('checkout.sucesso', ['pedido' => $pedido->id]);
        }

        // Se o pedido falhou ou foi cancelado, não tenta processar de novo
        if (in_array($pedido->status, ['falhou', 'cancelado'])) {
            return redirect()->route('cart.index')->with('error', 'O pagamento deste pedido falhou ou foi cancelado.');
        }
        
        // --- VERIFICAR PAGAMENTO (Passo 4 da Doc) ---
        $infinitepay_handle = 'guilhermecfrancellino'; 

        try {
            $response = Http::post("https://api.infinitepay.io/invoices/public/checkout/payment_check/{$infinitepay_handle}", [
                'handle' => $infinitepay_handle,
                'transaction_nsu' => $transaction_id,
                'external_order_nsu' => $order_nsu,
                'slug' => $slug,
            ]);

            $data = $response->json();

            // --- ATUALIZAR PEDIDO ---
            if (isset($data['success']) && $data['success'] === true && isset($data['paid']) && $data['paid'] === true) {
                // SUCESSO!
                $pedido->update([
                    'status' => 'pago',
                    'transaction_id' => $transaction_id,
                    'payment_method' => $capture_method,
                    'receipt_url' => $receipt_url,
                    'infinitepay_slug' => $slug,
                ]);

                return redirect()->route('checkout.sucesso', ['pedido' => $pedido->id]);

            } else {
                // FALHA! O pagamento não foi confirmado.
                $pedido->update(['status' => 'falhou']);
                // TODO: Restaurar o estoque (veja nota abaixo)
                return redirect()->route('cart.index')->with('error', 'O pagamento falhou ou foi cancelado.');
            }

        } catch (Exception $e) {
            // Lidar com erro na chamada da API de verificação
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