@extends('layouts.app')

@section('title', 'Pagamento Aprovado')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center shadow-sm">
                <div class="card-body p-5">
                    {{-- Ícone de Sucesso --}}
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    
                    <h1 class="h3">Obrigado pela sua compra!</h1>
                    <p class="lead text-muted">Seu pagamento foi aprovado com sucesso.</p>
                    
                    <hr class="my-4">
                    
                    {{-- Detalhes do Pedido --}}
                    <div class="alert alert-light border">
                        <h5 class="alert-heading">Pedido #{{ $pedido->id }}</h5>
                        <p class="mb-1">
                            <strong>Status:</strong> <span class="badge bg-success">{{ $pedido->status }}</span>
                        </p>
                        <p class="mb-0">
                            <strong>Total:</strong> R$ {{ number_format($pedido->total, 2, ',', '.') }}
                        </p>
                    </div>
                    
                    {{-- Ações para o Usuário --}}
                    <div class="mt-4">
                        @if($pedido->receipt_url)
                            <a href="{{ $pedido->receipt_url }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-receipt"></i> Ver Comprovante
                            </a>
                        @endif
                        <a href="{{ route('meus-pedidos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-box-open"></i> Ver Meus Pedidos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection