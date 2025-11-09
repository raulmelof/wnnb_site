<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Importante para fazer a chamada de API

class CheckoutController extends Controller
{
    /**
     * PASSO 2 (Doc InfinitePay): Criar o Pedido localmente
     * e redirecionar para o link de pagamento.
     */
    public function iniciarPagamento(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $user = Auth::user();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        // --- 1. Criar o Pedido no Banco de Dados ---
        $pedido = null;
        $total = 0;
        $items_para_infinitepay = [];

        try {
            // Usamos uma transação para garantir que o pedido e os itens 
            // sejam criados juntos, ou nada seja criado se der erro.
            DB::transaction(function () use ($cart, $user, &$pedido, &$total, &$items_para_infinitepay) {
                
                // Calcula o total e formata os itens para a API
                foreach ($cart as $id => $details) {
                    $subtotal = $details['preco'] * $details['quantidade'];
                    $total += $subtotal;

                    $items_para_infinitepay[] = [
                        'name' => $details['nome'],
                        // Preço deve ser em CENTAVOS para a InfinitePay
                        'price' => (int) ($details['preco'] * 100), 
                        'quantity' => $details['quantidade'],
                    ];
                }

                // Cria o Pedido com o novo status
                $pedido = Pedido::create([
                    'user_id' => $user->id,
                    'total' => $total,
                    'status' => 'aguardando_pagamento', // Nosso novo status
                ]);

                // Associa os produtos ao pedido (tabela pedido_produtos)
                foreach ($cart as $id => $details) {
                    $pedido->produtos()->attach($id, [
                        'quantidade' => $details['quantidade'],
                        'preco' => $details['preco']
                    ]);
                }
            });

        } catch (\Exception $e) {
            // TODO: Logar o erro real ($e->getMessage()) em um arquivo de log
            
            // Se algo der errado (ex: erro de banco), volta ao carrinho
            return redirect()->route('cart.index')->with('error', 'Erro ao processar seu pedido. Tente novamente.');
        }

        // --- 2. Limpar o Carrinho ---
        // Fazemos isso *depois* que o pedido foi criado com sucesso.
        $request->session()->forget('cart');

        // --- 3. Montar a URL de Pagamento (Passo 2 da Doc) ---

        $infinitepay_handle = 'guilhermecfrancellino'; 

        $params = [
            'handle' => $infinitepay_handle,
            'items' => json_encode($items_para_infinitepay),
            // Usamos o ID do nosso pedido como 'order_nsu'
            'order_nsu' => $pedido->id, 
            // O Laravel gera a URL completa para o callback
            'redirect_url' => route('checkout.callback'), 
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ];

        // Constrói a query string (ex: items=[...]&order_nsu=123&...)
        $query_string = http_build_query($params);

        $infinitepay_url = "https://checkout.infinitepay.io/{$infinitepay_handle}?{$query_string}";

        // --- 4. Redirecionar o Usuário ---
        return redirect()->away($infinitepay_url);
    }

    /**
     * PASSO 3 e 4 (Doc InfinitePay): Receber o callback,
     * verificar o pagamento e atualizar o pedido.
     */
    public function processarCallback(Request $request)
    {
        // 1. Receber os dados do callback (Passo 3 da Doc)
        $order_nsu = $request->input('order_nsu');
        $transaction_id = $request->input('transaction_id');
        $capture_method = $request->input('capture_method');
        $receipt_url = $request->input('receipt_url');
        $slug = $request->input('slug');

        // 2. Encontrar o pedido no nosso banco
        $pedido = Pedido::find($order_nsu);

        if (!$pedido) {
            // Logar o erro ou lidar com pedido não encontrado
            return redirect()->route('home')->with('error', 'Pedido não encontrado.');
        }

        // Evita que o pedido seja processado duas vezes
        if ($pedido->status === 'pago') {
             return redirect()->route('checkout.sucesso', ['pedido' => $pedido->id]);
        }
        
        // 3. Verificar o Pagamento (Passo 4 da Doc)
        
        $infinitepay_handle = 'guilhermecfrancellino'; 

        try {
            $response = Http::post("https://api.infinitepay.io/invoices/public/checkout/payment_check/{$infinitepay_handle}", [
                'handle' => $infinitepay_handle,
                'transaction_nsu' => $transaction_id,
                'external_order_nsu' => $order_nsu,
                'slug' => $slug,
            ]);

            $data = $response->json();

            // 4. Atualizar o Pedido
            if (isset($data['success']) && $data['success'] === true && isset($data['paid']) && $data['paid'] === true) {
                // SUCESSO! O pagamento foi confirmado.
                $pedido->update([
                    'status' => 'pago',
                    'transaction_id' => $transaction_id,
                    'payment_method' => $capture_method,
                    'receipt_url' => $receipt_url,
                    'infinitepay_slug' => $slug,
                ]);

                // Redireciona para a página de sucesso
                return redirect()->route('checkout.sucesso', ['pedido' => $pedido->id]);

            } else {
                // FALHA! O pagamento não foi confirmado.
                $pedido->update(['status' => 'falhou']);
                return redirect()->route('cart.index')->with('error', 'O pagamento falhou ou foi cancelado.');
            }

        } catch (\Exception $e) {
            // Lidar com erro na chamada da API
            return redirect()->route('home')->with('error', 'Erro ao verificar o pagamento. Contate o suporte.');
        }
    }

    /**
     * Página final de "Obrigado pela compra".
     */
    public function mostrarSucesso(Pedido $pedido)
    {
        // Garante que o usuário só veja seus próprios pedidos
        if ($pedido->user_id !== Auth::id()) {
            abort(403);
        }

        // Vamos criar esta view no próximo passo
        return view('checkout.sucesso', ['pedido' => $pedido]);
    }
}