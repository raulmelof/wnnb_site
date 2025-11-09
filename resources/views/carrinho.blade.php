@extends('layouts.app')

@section('title', 'Seu Carrinho de Compras')

@section('content')
    <div class="container mt-4">
        <h2>Seu Carrinho</h2>

        @if(!empty($cart))
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Produto</th>
                        <th scope="col">Preço</th>
                        <th scope="col">Quantidade</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $id => $details)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $details['imagem']) }}" width="50" height="50" class="me-3 rounded">
                                    <div>
                                        <span class="fw-bold">{{ $details['nome'] }}</span>
                                        {{-- LINHA ADICIONADA: Mostra o tamanho --}}
                                        <span class="d-block text-muted small">
                                            Tamanho: {{ $details['tamanho'] }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>R$ {{ number_format($details['preco'], 2, ',', '.') }}</td>
                            <td style="width: 150px;">
                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantidade" value="{{ $details['quantidade'] }}" class="form-control form-control-sm" min="1">
                                    <button type="submit" class="btn btn-outline-primary btn-sm ms-2">OK</button>
                                </form>
                            </td>
                            <td>
                                {{-- Formulário para remover o item --}}
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end">
                <h3>Total: R$ {{ number_format($total, 2, ',', '.') }}</h3>
                <form action="{{ route('checkout.iniciar') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">Ir para o Pagamento</button>
                </form>
            </div>

        @else
            <div class="alert alert-info">
                Seu carrinho está vazio.
            </div>
        @endif
    </div>
@endsection