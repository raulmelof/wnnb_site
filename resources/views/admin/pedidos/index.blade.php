@extends('layouts.app')

@section('title', 'Gerenciar Pedidos')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Pedidos Recebidos</h1>
        </div>

        @if($pedidos->isEmpty())
            <div class="alert alert-info">Nenhum pedido encontrado.</div>
        @else
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID do Pedido</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $pedido)
                        <tr>
                            <td>#{{ $pedido->id }}</td>
                            <td>{{ $pedido->user->name }}</td>
                            <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                            <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                            <td><span class="badge bg-primary">{{ $pedido->status }}</span></td>
                            <td>
                                <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-info">Ver Detalhes</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection