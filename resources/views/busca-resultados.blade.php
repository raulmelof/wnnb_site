@extends('layouts.app')

@section('title', "Resultados para: $termo")

@section('content')
    <div class="container mt-4">
        <div class="text-center mb-5">
            <h1 class="display-5">Resultados da busca por: "{{ $termo }}"</h1>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            {{-- A diretiva @forelse é uma forma elegante de fazer um loop 
                e tratar o caso de o array estar vazio --}}
            @forelse ($produtos as $produto)
                <div class="col">
                    {{-- Reutilizando nosso componente de card mais uma vez! --}}
                    <x-produto-card :produto="$produto" />
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Nenhum produto encontrado com o termo "{{ $termo }}".
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('home') }}" class="btn btn-secondary">&larr; Voltar para a Vitrine Principal</a>
        </div>
    </div>
@endsection