@extends('layouts.app')

@php
    $hideNavbar = true;
@endphp

@section('title', 'Página Inicial')

@section('content')

    <div id="fullbanner">
        {{-- Coloque o caminho para sua imagem de graffiti aqui --}}
        <img src="{{ asset('storage/imagens/banner.jpg') }}" alt="Wannabe Banner">
    </div>

    @include('layouts.navigation')

    <div class="container mt-4">
        <div id="vitrine" class="text-center my-5">
            <h1>Nossa Vitrine</h1>
            <p class="lead">Confira os lançamentos mais recentes da WNB.</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @forelse ($produtos as $produto)
                <div class="col">
                    <x-produto-card :produto="$produto" :estoque="$produto->total_estoque" />
                </div>
            @empty
                <div class="col">
                    <p>Nenhum produto encontrado no momento.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Adicionamos os links de paginação dentro do container também --}}
        @if(method_exists($produtos, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $produtos->links() }}
            </div>
        @endif
    </div>,
@endsection