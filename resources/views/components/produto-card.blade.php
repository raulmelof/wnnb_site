{{-- O @props agora define que esperamos 'produto' e 'estoque' --}}
@props(['produto', 'estoque' => 0])

{{-- Adicionamos um CSS rápido para a sobreposição --}}
<style>
    .card-esgotado {
        position: relative;
    }
    .card-esgotado::after {
        content: 'ESGOTADO';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.7); /* Fundo branco semitransparente */
        color: #dc3545; /* Vermelho */
        font-weight: bold;
        font-size: 1.2rem;
        backdrop-filter: blur(2px); /* Efeito de desfoque */
        border-radius: var(--bs-card-border-radius); /* Arredonda as bordas */
    }
    .card-esgotado .card-body .btn {
        pointer-events: none; /* Desabilita o clique no botão */
    }
</style>

{{-- Se o estoque for 0, adiciona a classe .card-esgotado --}}
<div class="card h-100 shadow-sm @if($estoque <= 0) card-esgotado @endif">
    
    {{-- A tag <a> agora só funciona se tiver estoque --}}
    <a href="{{ $estoque > 0 ? route('produto.show', $produto->id) : '#' }}">
        <img src="{{ asset('storage/' . $produto->imagem) }}" class="card-img-top" alt="{{ $produto->nome }}">
    </a>
    
    <div class="card-body text-center">
        <h5 class="card-title">{{ $produto->nome }}</h5>
        <p class="card-text fw-bold text-success">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
        
        {{-- O botão agora é desabilitado se não tiver estoque --}}
        <a href="{{ route('produto.show', $produto->id) }}" 
           class="btn btn-primary" 
           @if($estoque <= 0) aria-disabled="true" @endif>
            Ver Detalhes
        </a>
    </div>
</div>