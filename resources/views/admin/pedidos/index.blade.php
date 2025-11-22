@extends('layouts.app')

@section('title', 'Gerenciar Pedidos')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Gerenciar Pedidos</h1>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Cidade/UF</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pedidos as $pedido)
                            <tr>
                                <td class="fw-bold">#{{ $pedido->id }}</td>
                                <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $pedido->user->name }}<br>
                                    <small class="text-muted">{{ $pedido->user->email }}</small>
                                </td>
                                <td>
                                    @if($pedido->endereco_cidade)
                                        {{ $pedido->endereco_cidade }} - {{ $pedido->endereco_estado }}
                                    @else
                                        <span class="text-muted">Retirada/N/A</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                                <td>
                                    @switch($pedido->status)
                                        @case('pago')
                                            <span class="badge bg-success">PAGO</span>
                                            @break
                                        @case('aguardando_pagamento')
                                            <span class="badge bg-warning text-dark">AGUARDANDO</span>
                                            @break
                                        @case('enviado')
                                            <span class="badge bg-primary">ENVIADO</span>
                                            @break
                                        @case('cancelado')
                                        @case('falhou')
                                            <span class="badge bg-danger">{{ strtoupper($pedido->status) }}</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $pedido->status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">Nenhum pedido encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Paginação (se houver) --}}
        @if(method_exists($pedidos, 'links'))
            <div class="mt-3">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>
@endsection