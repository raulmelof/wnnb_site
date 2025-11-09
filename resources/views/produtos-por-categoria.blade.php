@extends('layouts.app')

{{-- O título da página será dinâmico, baseado na categoria --}}
@section('title', "Categoria: $categoria")

@section('content')
    <div class="container mt-4">
        <div class="text-center mb-5">
            <h1 class="display-5">Categoria: {{ $categoria }}</h1>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @forelse ($produtos as $produto)
                <div class="col">
                    {{-- Reutilizando o mesmo componente da página inicial! --}}
                    <x-produto-card :produto="$produto" />
                </div>
            @empty
                <div class="col">
                    <div class="alert alert-info">
                        Nenhum produto encontrado nesta categoria.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('home') }}" class="btn btn-secondary">&larr; Voltar para a Vitrine Principal</a>
        </div>
    </div>
@endsection