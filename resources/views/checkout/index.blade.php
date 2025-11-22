@extends('layouts.app')

@section('title', 'Finalizar Pedido')

@section('content')
<div class="container mt-4 mb-5">
    <h1>Finalizar Pedido</h1>
    
    <div class="row">
        {{-- Coluna do Endereço --}}
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Endereço de Entrega</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('checkout.processar') }}" method="POST" id="checkout-form">
                        @csrf
                        {{-- Passamos os dados do frete adiante --}}
                        <input type="hidden" name="frete_tipo" value="{{ $freteTipo }}">
                        <input type="hidden" name="frete_valor" value="{{ $freteValor }}">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">CEP</label>
                                <input type="text" name="cep" id="cep" class="form-control" maxlength="9" required>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <small class="text-muted" id="status-cep">Digite o CEP para buscar o endereço.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-9">
                                <label class="form-label">Rua / Avenida</label>
                                <input type="text" name="rua" id="rua" class="form-control bg-light" readonly required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Número</label>
                                <input type="text" name="numero" id="numero" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Bairro</label>
                                <input type="text" name="bairro" id="bairro" class="form-control bg-light" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Complemento (Opcional)</label>
                                <input type="text" name="complemento" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="cidade" id="cidade" class="form-control bg-light" readonly required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <input type="text" name="estado" id="estado" class="form-control bg-light" readonly required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Coluna do Resumo --}}
        <div class="col-md-5">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h4 class="card-title mb-4">Resumo do Pedido</h4>
                    
                    {{-- Lista Simples de Produtos --}}
                    <ul class="list-group list-group-flush mb-3 bg-transparent">
                        @foreach($cart as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                <div>
                                    {{ $item['nome'] }} <br>
                                    <small class="text-muted">{{ $item['tamanho'] }} x {{ $item['quantidade'] }}</small>
                                </div>
                                <span>R$ {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    
                    @if($desconto > 0)
                        <div class="d-flex justify-content-between text-success">
                            <span>Desconto</span>
                            <span>- R$ {{ number_format($desconto, 2, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <span>Frete ({{ $freteTipo }})</span>
                        <span>R$ {{ number_format($freteValor, 2, ',', '.') }}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <strong class="fs-4">Total</strong>
                        <strong class="fs-4 text-success">R$ {{ number_format($total, 2, ',', '.') }}</strong>
                    </div>

                    <button type="submit" form="checkout-form" class="btn btn-success w-100 btn-lg">
                        Pagar Agora <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cepInput = document.getElementById('cep');
        
        // Máscara de CEP
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length > 5) value = value.replace(/^(\d{5})(\d)/, "$1-$2");
            e.target.value = value;
            
            if (value.length === 9) buscarEndereco(value);
        });

        function buscarEndereco(cep) {
            document.getElementById('status-cep').innerText = "Buscando...";
            
            fetch(`https://viacep.com.br/ws/${cep.replace('-', '')}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        document.getElementById('estado').value = data.uf;
                        document.getElementById('status-cep').innerText = "Endereço encontrado!";
                        document.getElementById('numero').focus(); // Pula pro número
                    } else {
                        document.getElementById('status-cep').innerText = "CEP não encontrado.";
                        limparFormulario();
                    }
                })
                .catch(() => {
                    document.getElementById('status-cep').innerText = "Erro ao buscar CEP.";
                });
        }

        function limparFormulario() {
            document.getElementById('rua').value = "";
            document.getElementById('bairro').value = "";
            document.getElementById('cidade').value = "";
            document.getElementById('estado').value = "";
        }
    });
</script>
@endsection