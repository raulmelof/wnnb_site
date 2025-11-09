@extends('layouts.app')

@section('title', 'Adicionar Novo Produto')

@section('content')

    <div class="container mt-4">
        <h1>Adicionar Novo Produto</h1>

        {{-- Exibe erros de validação --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.produtos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
            </div>
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoria</label>
                <input type="text" class="form-control" id="categoria" name="categoria" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem do Produto</label>
                <input class="form-control" type="file" id="imagem" name="imagem">
            </div>
            
            <button type="submit" class="btn btn-primary">Salvar Produto</button>
        </form>
    </div>
@endsection