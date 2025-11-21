@extends('layouts.app')

@section('title', 'Gerenciar Cupons')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Cupons de Desconto</h1>
        <a href="{{ route('admin.cupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Cupom
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Desconto</th>
                        <th>Validade</th>
                        <th>Usos</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cupons as $cupom)
                        <tr>
                            <td class="fw-bold">{{ $cupom->codigo }}</td>
                            <td>
                                @if($cupom->tipo == 'percentual')
                                    {{ number_format($cupom->valor, 0) }}%
                                @else
                                    R$ {{ number_format($cupom->valor, 2, ',', '.') }}
                                @endif
                            </td>
                            <td>
                                {{ $cupom->validade ? \Carbon\Carbon::parse($cupom->validade)->format('d/m/Y') : 'Indefinida' }}
                            </td>
                            <td>
                                {{ $cupom->usos_atuais }} 
                                @if($cupom->limite_uso) / {{ $cupom->limite_uso }} @endif
                            </td>
                            <td>
                                @if($cupom->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.cupons.edit', $cupom->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                
                                <form action="{{ route('admin.cupons.destroy', $cupom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Nenhum cupom cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection