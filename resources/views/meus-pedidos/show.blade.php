@extends('layouts.app')

@section('title', 'Detalhes do Pedido #' . $pedido->id)

@section('content')

    <div class="container mt-4">
        <h1>Detalhes do Pedido #{{ $pedido->id }}</h1>
        <p><strong>Data:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Status:</strong> <span class="badge bg-primary">{{ $pedido->status }}</span></p>

        <div class="card mt-4">
            <div class="card-header">
                Itens do Pedido
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($pedido->produtos as $produto)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $produto->nome }} ({{ $produto->pivot->quantidade }}x)
                        <span>R$ {{ number_format($produto->pivot->preco, 2, ',', '.') }} cada</span>
                    </li>
                @endforeach
            </ul>
            <div class="card-footer fw-bold text-end">
                Total: R$ {{ number_format($pedido->total, 2, ',', '.') }}
            </div>
        </div>
        <a href="{{ route('meus-pedidos.index') }}" class="btn btn-secondary mt-3">&larr; Voltar para Meus Pedidos</a>
    </div>
@endsection