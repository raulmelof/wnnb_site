@extends('layouts.app')

@section('title', 'Meus Pedidos')

@section('content')

    <div class="container mt-4">
        <h1>Meus Pedidos</h1>
        <p>Veja abaixo seu histórico de compras.</p>

        @forelse ($pedidos as $pedido)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <span>Pedido #{{ $pedido->id }}</span>
                    <span>Data: {{ $pedido->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total: R$ {{ number_format($pedido->total, 2, ',', '.') }}</h5>
                        <p class="card-text">Status: <span class="badge bg-primary">{{ $pedido->status }}</span></p>
                    </div>
                    <a href="{{ route('meus-pedidos.show', $pedido->id) }}" class="btn btn-outline-primary">Ver Detalhes</a>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Você ainda não fez nenhum pedido.
            </div>
        @endforelse
    </div>
@endsection