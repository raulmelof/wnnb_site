<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // ... método index() que já fizemos ...
    public function index()
    {
        $pedidos = Pedido::with('user')->latest()->get();
        return view('admin.pedidos.index', ['pedidos' => $pedidos]);
    }

    /**
     * Mostra os detalhes de um pedido específico.
     */
    public function show(Pedido $pedido)
    {
        // Carrega os relacionamentos para ter acesso aos dados do usuário e dos produtos
        $pedido->load('user', 'produtos');
        return view('admin.pedidos.show', ['pedido' => $pedido]);
    }

    /**
     * Atualiza o status de um pedido.
     */
    public function update(Request $request, Pedido $pedido)
    {
        // Valida se o status enviado é um dos valores permitidos
        $request->validate([
            'status' => 'required|in:pendente,pago,enviado,cancelado'
        ]);

        // Atualiza o status do pedido
        $pedido->status = $request->input('status');
        $pedido->save();

        // Redireciona de volta para a página de detalhes com uma mensagem de sucesso
        return redirect()->route('admin.pedidos.show', $pedido->id)->with('success', 'Status do pedido atualizado!');
    }
}