@extends('layouts.app')

@section('title', $produto->nome)

@section('content')
    <div class="container mt-4">
        <div class="row">
            {{-- Coluna da Imagem --}}
            <div class="col-md-6">
                <img src="{{ asset('storage/' . $produto->imagem) }}" class="img-fluid rounded shadow-sm" alt="{{ $produto->nome }}">
            </div>

            {{-- Coluna das Informações --}}
            <div class="col-md-6">
                <h1>{{ $produto->nome }}</h1>
                <p class="lead text-muted">{{ $produto->categoria }}</p>
                <h3 class="fw-bold text-success my-3">R$ {{ number_format($produto->preco, 2, ',', '.') }}</h3>

                <p>{{ $produto->descricao }}</p>

                {{-- Formulário de Adicionar ao Carrinho --}}
                <form action="{{ route('cart.add') }}" method="POST" id="cart-form">
                    @csrf
                    {{-- Input escondido para o ID do produto (para o CartController) --}}
                    <input type="hidden" name="produto_id" value="{{ $produto->id }}">

                    <div class="mb-3">
                        <label for="variacao" class="form-label"><strong>Escolha o Tamanho:</strong></label>
                        <select class="form-select" id="variacao" name="variacao_id" required>
                            <option value="" selected disabled>Selecione um tamanho</option>
                            
                            {{-- Loop para mostrar apenas tamanhos com estoque --}}
                            @foreach ($produto->variacoes as $variacao)
                                @if ($variacao->estoque > 0)
                                    <option value="{{ $variacao->id }}">
                                        {{ $variacao->tamanho }} ({{ $variacao->estoque }} em estoque)
                                    </option>
                                @else
                                    <option value="{{ $variacao->id }}" disabled>
                                        {{ $variacao->tamanho }} (Esgotado)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="add-to-cart-btn">
                            Adicionar ao Carrinho
                        </button>
                    </div>
                </form>

                <div class="mt-3">
                    <x-botao-voltar />
                </div>
            </div>
        </div>
    </div>
@endsection