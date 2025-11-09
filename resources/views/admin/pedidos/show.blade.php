@extends('layouts.app')

@section('title', 'Detalhes do Pedido #' . $pedido->id)

@section('content')

    <div class="container mt-4">
        <h1>Detalhes do Pedido #{{ $pedido->id }}</h1>

        <div class="row">
            {{-- Coluna de Informações do Pedido --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Informações do Cliente</div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> {{ $pedido->user->name }}</p>
                        <p><strong>Email:</strong> {{ $pedido->user->email }}</p>
                        <p><strong>Data do Pedido:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">Atualizar Status do Pedido</div>
                    <div class="card-body">
                        <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="input-group">
                                <select name="status" class="form-select">
                                    <option value="pendente" @selected($pedido->status == 'pendente')>Pendente</option>
                                    <option value="pago" @selected($pedido->status == 'pago')>Pago</option>
                                    <option value="enviado" @selected($pedido->status == 'enviado')>Enviado</option>
                                    <option value="cancelado" @selected($pedido->status == 'cancelado')>Cancelado</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Salvar Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Coluna de Itens do Pedido --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Itens Comprados</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($pedido->produtos as $produto)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <img src="{{ asset('storage/' . $produto->imagem) }}" width="40" class="me-2 rounded">
                                    {{ $produto->nome }}
                                </div>
                                <span>{{ $produto->pivot->quantidade }} x R$ {{ number_format($produto->pivot->preco, 2, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="card-footer text-end">
                        <strong>Total do Pedido: R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-secondary mt-4">&larr; Voltar para a Lista de Pedidos</a>
    </div>
@endsection