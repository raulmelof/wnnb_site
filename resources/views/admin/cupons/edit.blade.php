@extends('layouts.app')

@section('title', 'Editar Cupom')

@section('content')
<div class="container mt-4">
    <h1>Editar Cupom: {{ $cupom->codigo }}</h1>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('admin.cupons.update', $cupom->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Código do Cupom</label>
                        {{-- 'old' recupera o valor se a validação falhar, senão usa o do banco --}}
                        <input type="text" name="codigo" class="form-control text-uppercase" 
                               value="{{ old('codigo', $cupom->codigo) }}" required>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            {{-- Checa se o cupom está ativo para marcar o checkbox --}}
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" 
                                   {{ old('ativo', $cupom->ativo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ativo">Cupom Ativo</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Desconto</label>
                        <select name="tipo" class="form-select" required>
                            <option value="percentual" {{ old('tipo', $cupom->tipo) == 'percentual' ? 'selected' : '' }}>
                                Porcentagem (%)
                            </option>
                            <option value="fixo" {{ old('tipo', $cupom->tipo) == 'fixo' ? 'selected' : '' }}>
                                Valor Fixo (R$)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Valor do Desconto</label>
                        <input type="number" step="0.01" name="valor" class="form-control" 
                               value="{{ old('valor', $cupom->valor) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Validade (Opcional)</label>
                        <input type="date" name="validade" class="form-control" 
                               value="{{ old('validade', $cupom->validade) }}">
                        <small class="text-muted">Deixe em branco para nunca expirar.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Limite de Usos (Opcional)</label>
                        <input type="number" name="limite_uso" class="form-control" 
                               value="{{ old('limite_uso', $cupom->limite_uso) }}">
                        <small class="text-muted">
                            Usos atuais: <strong>{{ $cupom->usos_atuais }}</strong>. Deixe em branco para ilimitado.
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">Atualizar Cupom</button>
                <a href="{{ route('admin.cupons.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection