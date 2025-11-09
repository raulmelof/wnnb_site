@extends('layouts.app')

@section('title', $produto->nome)

{{-- CSS Customizado para os botões de tamanho --}}
<style>
    .size-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    /* Esconde o input de rádio real */
    .size-selector input[type="radio"] {
        display: none;
    }

    /* Estiliza o label (que parece um botão) */
    .size-selector label {
        display: inline-block;
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    /* Estilo do botão quando selecionado (ativo) */
    .size-selector input[type="radio"]:checked + label {
        background-color: #0d6efd; /* Azul do Bootstrap */
        color: white;
        border-color: #0d6efd;
    }

    /* Estilo do botão quando desabilitado (esgotado) */
    .size-selector input[type="radio"]:disabled + label {
        background-color: #f8f9fa; /* Cinza claro */
        color: #adb5bd;
        cursor: not-allowed;
        border-color: #f8f9fa;
        position: relative;
        overflow: hidden; /* Para o "risco" */
    }

    /* O "risco na diagonal" que você pediu */
    .size-selector input[type="radio"]:disabled + label::after {
        content: '';
        position: absolute;
        top: 50%;
        left: -10%;
        width: 120%;
        height: 1px;
        background-color: #adb5bd;
        transform: rotate(-20deg) translateY(-50%);
    }

    /* Efeito hover (apenas para botões não-desabilitados) */
    .size-selector input[type="radio"]:not(:disabled):hover + label {
        background-color: #e9ecef;
    }
</style>

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
                    <input type="hidden" name="produto_id" value="{{ $produto->id }}">

                    <div class="mb-3">
                        <label class="form-label"><strong>Escolha o Tamanho:</strong></label>
                        
                        <div class="size-selector">
                            @forelse ($produto->variacoes as $variacao)
                                <div class="size-btn">
                                    {{-- O Input de Rádio (escondido) --}}
                                    <input type="radio" 
                                           name="variacao_id" 
                                           id="size-{{ $variacao->id }}" 
                                           value="{{ $variacao->id }}"
                                           {{-- Desabilita se o estoque for 0 --}}
                                           @if($variacao->estoque <= 0) disabled @endif
                                           required>
                                    
                                    {{-- O Label (que o usuário vê como botão) --}}
                                    <label for="size-{{ $variacao->id }}">
                                        {{ $variacao->tamanho }}
                                    </label>
                                </div>
                            @empty
                                <div class="alert alert-warning">
                                    Nenhum tamanho disponível para este produto.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="add-to-cart-btn" disabled>
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

    {{-- Script para habilitar o botão --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addToCartButton = document.getElementById('add-to-cart-btn');
            const sizeSelector = document.querySelector('.size-selector');

            // Habilita o botão "Adicionar ao Carrinho" assim que um tamanho é selecionado
            sizeSelector.addEventListener('change', function(e) {
                if (e.target.type === 'radio' && e.target.name === 'variacao_id') {
                    addToCartButton.disabled = false;
                }
            });
        });
    </script>
@endsection