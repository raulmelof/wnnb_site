@extends('layouts.app') {{-- Usando o mesmo layout principal --}}

@section('title', 'Gerenciar Produtos')

@section('content')

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Produtos</h1>
            <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary">Adicionar Produto</a>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produtos as $produto)
                    <tr>
                        <td>{{ $produto->id }}</td>
                        <td>
                            <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" width="60">
                        </td>
                        <td>{{ $produto->nome }}</td>
                        <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.produtos.edit', $produto->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('admin.produtos.destroy', $produto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection