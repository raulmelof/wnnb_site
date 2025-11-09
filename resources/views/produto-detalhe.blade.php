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

                <div class="d-grid gap-2 mt-4">
                    {{-- Substitua o <button> antigo por este formulário --}}
                    <form action="{{ route('cart.add', $produto->id) }}" method="POST">
                        @csrf {{-- Diretiva de segurança obrigatória do Laravel --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100">Adicionar ao Carrinho</button>
                    </form>
                </div>

                {{-- Aqui usamos o nosso novo componente anônimo sem parâmetros! --}}
                <div class="mt-3">
                    <x-botao-voltar />
                </div>
            </div>
        </div>
    </div>
@endsection