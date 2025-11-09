@extends('layouts.app') {{-- Ou seu layout de admin, ex: 'templates.base-template' --}}

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

        <form action="{{ route('admin.produtos.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Importante para o método 'update' --}}
            
            {{-- Dados Principais --}}
            <div class="card mb-3">
                <div class="card-header">Dados Principais</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $produto->nome) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço (Base)</label>
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
                        <label for="imagem" class="form-label">Imagem do Produto</label>
                        <input class="form-control" type="file" id="imagem" name="imagem">
                        @if ($produto->imagem)
                            <small class="text-muted d-block mt-2">Imagem atual:</small>
                            <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" height="100">
                        @endif
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
                        {{-- Loop para preencher as variações existentes --}}
                        @foreach ($produto->variacoes as $index => $variacao)
                            <div class="row variacao-row mb-2">
                                {{-- Input escondido com o ID da variação --}}
                                <input type="hidden" name="variacoes[{{ $index }}][id]" value="{{ $variacao->id }}">

                                <div class="col-md-5">
                                    <input type="text" name="variacoes[{{ $index }}][tamanho]" class="form-control" 
                                           placeholder="Tamanho (ex: P, M, 8.5)" value="{{ $variacao->tamanho }}" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" name="variacoes[{{ $index }}][estoque]" class="form-control" 
                                           placeholder="Estoque" min="0" value="{{ $variacao->estoque }}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-variacao">Remover</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Atualizar Produto</button>
        </form>
    </div>

    {{-- Template para o JavaScript (escondido) --}}
    <template id="variacao-template">
        <div class="row variacao-row mb-2">
            {{-- O ID aqui é nulo, pois é uma nova variação --}}
            <input type="hidden" name="variacoes[__INDEX__][id]" value="">

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
            
            // O index inicial deve ser o número de variações já existentes
            let index = {{ $produto->variacoes->count() }};

            // Evento para remover linhas (tanto as existentes quanto as novas)
            container.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-variacao')) {
                    e.target.closest('.variacao-row').remove();
                }
            });

            function addVariacaoRow() {
                const clone = template.content.cloneNode(true);
                const newRow = clone.firstElementChild;
                newRow.innerHTML = newRow.innerHTML.replace(/__INDEX__/g, index);
                
                // O evento de remoção agora é gerenciado pelo 'container'
                
                container.appendChild(newRow);
                index++;
            }

            addButton.addEventListener('click', addVariacaoRow);
        });
    </script>
@endsection