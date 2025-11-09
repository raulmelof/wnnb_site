<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Salva um novo pedido no banco de dados.
     */
    public function index()
    {
        // Pega o usuário logado e busca seus pedidos, ordenando pelos mais recentes
        $pedidos = Auth::user()->pedidos()->latest()->get();

        return view('meus-pedidos.index', ['pedidos' => $pedidos]);
    }

    /**
     * Mostra os detalhes de um pedido específico.
     */
    public function show(Pedido $pedido)
    {
        // VERIFICAÇÃO DE SEGURANÇA: Garante que o usuário logado
        // só pode ver os seus próprios pedidos.
        if ($pedido->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.'); // Retorna um erro 403 Proibido
        }

        // Carrega os produtos associados a este pedido para evitar múltiplas queries
        $pedido->load('produtos');

        return view('meus-pedidos.show', ['pedido' => $pedido]);
    }
    
     public function store(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        // Usa uma transação para garantir que todas as operações sejam bem-sucedidas
        DB::transaction(function () use ($request, $cart) {
            // Calcula o total
            $total = 0;
            foreach ($cart as $id => $details) {
                $total += $details['preco'] * $details['quantidade'];
            }

            // Cria o pedido
            $pedido = Pedido::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pendente',
            ]);

            // Associa os produtos do carrinho ao pedido recém-criado
            foreach ($cart as $id => $details) {
                $pedido->produtos()->attach($id, [
                    'quantidade' => $details['quantidade'],
                    'preco' => $details['preco'] // Salva o preço no momento da compra
                ]);
            }
        });
        
        // Limpa o carrinho da sessão
        $request->session()->forget('cart');

        // Redireciona com mensagem de sucesso
        return redirect()->route('home')->with('success', 'Pedido realizado com sucesso! Obrigado pela sua compra.');
    }
}