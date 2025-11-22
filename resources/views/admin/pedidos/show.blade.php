@extends('layouts.app')

@section('title', 'Pedido #' . $pedido->id)

@section('content')
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            Pedido #{{ $pedido->id }} 
            <span class="fs-5 text-muted">em {{ $pedido->created_at->format('d/m/Y H:i') }}</span>
        </h1>
        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-secondary">&larr; Voltar</a>
    </div>

    <div class="row">
        {{-- COLUNA DA ESQUERDA: Detalhes do Pedido e Itens --}}
        <div class="col-md-8">
            
            {{-- Card de Status e Rastreio --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Gerenciar Envio</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select">
                                    <option value="pago" {{ $pedido->status == 'pago' ? 'selected' : '' }}>Pago (Separar)</option>
                                    <option value="enviado" {{ $pedido->status == 'enviado' ? 'selected' : '' }}>Enviado</option>
                                    <option value="cancelado" {{ $pedido->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                            
                            <div class="col-md-5">
                                <label class="form-label small">Código de Rastreio</label>
                                <input type="text" name="codigo_rastreio" class="form-control" 
                                       placeholder="Ex: OJ123456BR" 
                                       value="{{ $pedido->codigo_rastreio }}">
                            </div>

                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Card de Itens --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Itens do Pedido</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($pedido->produtos as $produto)
                        <li class="list-group-item d-flex align-items-center">
                            {{-- Imagem Pequena --}}
                            <img src="{{ asset('storage/' . $produto->imagem) }}" width="50" height="50" class="rounded me-3" style="object-fit: cover;">
                            
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $produto->nome }}</h6>
                                {{-- Aqui mostramos o TAMANHO --}}
                                {{-- Precisamos acessar a tabela pivot 'pedido_produtos' para pegar o 'produto_variacao_id' --}}
                                @php
                                    // Gambiarra elegante para pegar o tamanho:
                                    // O Laravel carrega o pivot, mas precisamos achar o nome do tamanho na tabela de variações
                                    $variacao = \App\Models\ProdutoVariacao::find($produto->pivot->produto_variacao_id);
                                    $tamanho = $variacao ? $variacao->tamanho : 'N/A';
                                @endphp
                                <small class="text-muted">Tamanho: <strong>{{ $tamanho }}</strong></small>
                            </div>
                            
                            <div class="text-end">
                                <div class="fw-bold">{{ $produto->pivot->quantidade }}x R$ {{ number_format($produto->pivot->preco, 2, ',', '.') }}</div>
                                <small class="text-muted">Total: R$ {{ number_format($produto->pivot->quantidade * $produto->pivot->preco, 2, ',', '.') }}</small>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Total do Pedido</span>
                    <span class="fs-4 text-success fw-bold">R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- COLUNA DA DIREITA: Informações de Entrega e Pagamento --}}
        <div class="col-md-4">
            
            {{-- Card de Cliente e Entrega --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Entrega</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">{{ $pedido->user->name }}</h6>
                    <p class="text-muted mb-3">{{ $pedido->user->email }}</p>
                    
                    <hr>

                    @if($pedido->endereco_rua)
                        <address class="mb-0">
                            <strong>{{ $pedido->endereco_rua }}, {{ $pedido->endereco_numero }}</strong><br>
                            @if($pedido->endereco_complemento)
                                {{ $pedido->endereco_complemento }}<br>
                            @endif
                            {{ $pedido->endereco_bairro }}<br>
                            {{ $pedido->endereco_cidade }} - {{ $pedido->endereco_estado }}<br>
                            CEP: {{ $pedido->endereco_cep }}
                        </address>
                        <div class="mt-3 d-grid gap-2">
                        <a href="{{ route('admin.pedidos.etiqueta', $pedido->id) }}" target="_blank" class="btn btn-dark">
                            <i class="fas fa-print"></i> Imprimir Etiqueta
                        </a>
                        <div class="mt-3 d-grid">
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($pedido->endereco_rua . ', ' . $pedido->endereco_numero . ' - ' . $pedido->endereco_cidade) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-map-marker-alt"></i> Ver no Maps
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-store"></i> Retirada no Local / A Combinar
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card de Pagamento InfinitePay --}}
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Pagamento (InfinitePay)</h5>
                </div>
                <div class="card-body">
                    <p><strong>Transação ID:</strong><br> 
                        <span class="text-monospace bg-light px-1">{{ $pedido->transaction_id ?? 'Pendente' }}</span>
                    </p>
                    <p><strong>Método:</strong> {{ ucfirst($pedido->payment_method ?? '-') }}</p>
                    
                    @if($pedido->receipt_url)
                        <div class="d-grid">
                            <a href="{{ $pedido->receipt_url }}" target="_blank" class="btn btn-info text-white">
                                <i class="fas fa-receipt"></i> Ver Comprovante
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection