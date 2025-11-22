<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PedidoEnviado;

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
        $dados = $request->validate([
            'status' => 'required|in:pago,aguardando_pagamento,enviado,cancelado,falhou',
            'codigo_rastreio' => 'nullable|string|max:50', // Novo campo
        ]);

        // Verifica se o status está mudando para 'enviado' AGORA
        $mudouParaEnviado = ($pedido->status !== 'enviado' && $dados['status'] === 'enviado');

        $pedido->update($dados);

        // Dispara e-mail se acabou de ser enviado
        if ($mudouParaEnviado) {
            try {
                Mail::to($pedido->user->email)->send(new PedidoEnviado($pedido));
            } catch (\Exception $e) {
                // Log erro de e-mail, mas não trava o admin
            }
        }

        return redirect()->route('admin.pedidos.show', $pedido->id)
                         ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function imprimirEtiqueta(Pedido $pedido)
    {
        return view('admin.pedidos.etiqueta', ['pedido' => $pedido]);
    }
}