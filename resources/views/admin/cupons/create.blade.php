@extends('layouts.app')

@section('title', 'Novo Cupom')

@section('content')
<div class="container mt-4">
    <h1>Criar Novo Cupom</h1>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('admin.cupons.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Código do Cupom</label>
                        <input type="text" name="codigo" class="form-control text-uppercase" placeholder="Ex: VERÃO2025" required>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" checked>
                            <label class="form-check-label" for="ativo">Cupom Ativo</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Desconto</label>
                        <select name="tipo" class="form-select" required>
                            <option value="percentual">Porcentagem (%)</option>
                            <option value="fixo">Valor Fixo (R$)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Valor do Desconto</label>
                        <input type="number" step="0.01" name="valor" class="form-control" placeholder="Ex: 10.00" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Validade (Opcional)</label>
                        <input type="date" name="validade" class="form-control">
                        <small class="text-muted">Deixe em branco para nunca expirar.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Limite de Usos (Opcional)</label>
                        <input type="number" name="limite_uso" class="form-control" placeholder="Ex: 100">
                        <small class="text-muted">Deixe em branco para uso ilimitado.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Salvar Cupom</button>
                <a href="{{ route('admin.cupons.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection