<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // O método add() que já fizemos...
    public function add(Request $request, Produto $produto)
    {
        $cart = $request->session()->get('cart', []);

        // Verifica se o produto já está no carrinho
        if(isset($cart[$produto->id])) {
            // Se sim, incrementa a quantidade
            $cart[$produto->id]['quantidade']++;
        } else {
            // Se não, adiciona o produto com quantidade 1
            $cart[$produto->id] = [
                "nome" => $produto->nome,
                "preco" => $produto->preco,
                "imagem" => $produto->imagem,
                "quantidade" => 1
            ];
        }

        // Salva o carrinho atualizado de volta na sessão
        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    // O método index() que já fizemos...
    public function index()
    {
        $cart = session('cart', []);
        
        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['preco'] * $details['quantidade'];
        }

        return view('carrinho', ['cart' => $cart, 'total' => $total]);
    }

    /**
     * Remove um item do carrinho.
     */
    public function remove(Request $request, $produto_id)
    {
        // Pega o carrinho da sessão
        $cart = $request->session()->get('cart');

        // Verifica se o carrinho existe e se o item está nele
        if(isset($cart[$produto_id])) {
            // Remove o item do array do carrinho usando a função unset do PHP
            unset($cart[$produto_id]);

            // Salva o carrinho atualizado de volta na sessão
            $request->session()->put('cart', $cart);
        }

        // Redireciona de volta para a página do carrinho com uma mensagem
        return redirect()->route('cart.index')->with('success', 'Produto removido do carrinho.');
    }

    public function update(Request $request, $produto_id)
    {
        $cart = $request->session()->get('cart');

        // Validação para garantir que a quantidade é um número válido e maior que zero
        $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        // Se o item existir no carrinho, atualiza sua quantidade
        if(isset($cart[$produto_id])) {
            $cart[$produto_id]['quantidade'] = $request->quantidade;
            $request->session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Quantidade atualizada com sucesso!');
    }
}