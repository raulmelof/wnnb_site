@extends('layouts.app')

@section('title', 'Editar Produto: ' . $produto->nome)

@section('content')

    <div class="container mt-4">
        <h1>Editar Produto: {{ $produto->nome }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Note a rota 'update' e o método 'PUT' --}}
        <form action="{{ route('admin.produtos.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Diretiva para informar ao Laravel que esta é uma requisição de atualização --}}

            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                {{-- O helper 'old()' recupera o valor antigo em caso de falha na validação --}}
                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $produto->nome) }}" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="{{ old('preco', $produto->preco) }}" required>
            </div>
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoria</label>
                <input type="text" class="form-control" id="categoria" name="categoria" value="{{ old('categoria', $produto->categoria) }}" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao', $produto->descricao) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Nova Imagem do Produto (Opcional)</label>
                <input class="form-control" type="file" id="imagem" name="imagem">
                @if ($produto->imagem)
                    <small class="d-block mt-2">Imagem atual:</small>
                    <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" width="100">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
@endsection