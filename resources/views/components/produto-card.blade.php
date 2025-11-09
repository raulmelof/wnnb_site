{{-- A diretiva @props define quais parâmetros (variáveis) este componente espera receber --}}
@props(['produto'])

<div class="card h-100 shadow-sm">
    {{-- A tag de âncora leva para a rota de detalhes do produto --}}
    <a href="{{ route('produto.show', $produto->id) }}">
        {{-- O helper asset() cria a URL correta para arquivos na pasta 'public' --}}
        <img src="{{ asset('storage/' . $produto->imagem) }}" class="card-img-top" alt="{{ $produto->nome }}">
    </a>
    
    <div class="card-body text-center">
        <h5 class="card-title">{{ $produto->nome }}</h5>
        <p class="card-text fw-bold text-success">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
        <a href="{{ route('produto.show', $produto->id) }}" class="btn btn-primary">Ver Detalhes</a>
    </div>
</div>