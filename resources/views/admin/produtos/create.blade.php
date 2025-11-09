@extends('layouts.app') {{-- Ou seu layout de admin, ex: 'templates.base-template' --}}

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
            
            {{-- Dados Principais do Produto --}}
            <div class="card mb-3">
                <div class="card-header">Dados Principais</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço (Base)</label>
                        <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="{{ old('preco') }}" required>
                        <small class="text-muted">Este é o preço base do produto. Se os tamanhos tiverem preços diferentes, implementaremos isso no futuro.</small>
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria</label>
                        <input type="text" class="form-control" id="categoria" name="categoria" value="{{ old('categoria') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="imagem" class="form-label">Imagem do Produto</label>
                        <input class="form-control" type="file" id="imagem" name="imagem">
                    </div>
                </div>
            </div>

            {{-- Variações (Tamanho e Estoque) --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Variações (Tamanho e Estoque)
                    <button type="button" class="btn btn-sm btn-success" id="add-variacao">Adicionar Tamanho</button>
                </div>
                <div class="card-body">
                    <div id="variacoes-container">
                        {{-- O JavaScript irá adicionar linhas aqui --}}
                        {{-- Linha de exemplo (será o template) --}}
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Salvar Produto</button>
        </form>
    </div>

    {{-- Template para o JavaScript (escondido) --}}
    <template id="variacao-template">
        <div class="row variacao-row mb-2">
            <div class="col-md-5">
                <input type="text" name="variacoes[__INDEX__][tamanho]" class="form-control" placeholder="Tamanho (ex: P, M, 8.5)" required>
            </div>
            <div class="col-md-5">
                <input type="number" name="variacoes[__INDEX__][estoque]" class="form-control" placeholder="Estoque" min="0" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-variacao">Remover</button>
            </div>
        </div>
    </template>


    {{-- Script para o formulário dinâmico --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('variacoes-container');
            const template = document.getElementById('variacao-template');
            const addButton = document.getElementById('add-variacao');
            let index = 0;

            function addVariacaoRow() {
                // Clona o template
                const clone = template.content.cloneNode(true);
                
                // Substitui o placeholder __INDEX__ pelo index real
                const newRow = clone.firstElementChild;
                newRow.innerHTML = newRow.innerHTML.replace(/__INDEX__/g, index);
                
                // Adiciona o evento de remoção
                newRow.querySelector('.remove-variacao').addEventListener('click', function () {
                    this.closest('.variacao-row').remove();
                });

                container.appendChild(newRow);
                index++;
            }

            // Adiciona o evento ao botão
            addButton.addEventListener('click', addVariacaoRow);

            // Adiciona uma linha inicial ao carregar
            addVariacaoRow();
        });
    </script>
@endsection