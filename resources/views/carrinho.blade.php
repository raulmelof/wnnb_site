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
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row mt-4">
                {{-- Coluna Vazia (Espaçamento) --}}
                <div class="col-md-6"></div>

                {{-- Coluna de Totais e Cálculos --}}
                <div class="col-md-6">
                    
                    {{-- BLOCO DE CUPOM --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Cupom de Desconto</h5>
                            
                            <div class="input-group mb-3" id="grupo-input-cupom">
                                <input type="text" id="cupom-input" class="form-control text-uppercase" placeholder="Código do cupom">
                                <button class="btn btn-outline-primary" type="button" id="btn-aplicar-cupom">Aplicar</button>
                            </div>

                            <div id="cupom-sucesso" class="alert alert-success d-flex justify-content-between align-items-center" style="display: none;">
                                <span>
                                    Desconto: <strong>- R$ <span id="valor-desconto-display">0,00</span></strong>
                                    <br>
                                    <small id="codigo-cupom-display" class="text-uppercase"></small>
                                </span>
                                <button class="btn btn-sm btn-link text-danger p-0" id="btn-remover-cupom">Remover</button>
                            </div>
                            
                            <div id="cupom-erro" class="text-danger mt-2" style="display: none;"></div>
                        </div>
                    </div>

                    {{-- BLOCO DE FRETE --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Calcular Frete</h5>
                            <div class="input-group mb-3">
                                <input type="text" id="cep-input" class="form-control" placeholder="Digite seu CEP" maxlength="9">
                                <button class="btn btn-outline-secondary" type="button" id="btn-calcular-frete">Calcular</button>
                            </div>
                            
                            <div id="opcoes-frete" class="list-group"></div>
                            
                            {{-- TOTAL FINAL --}}
                            <div class="mt-4 text-end border-top pt-3">
                                <h5 class="text-muted">Subtotal: R$ {{ number_format($total, 2, ',', '.') }}</h5>
                                <h4 id="total-final" class="fw-bold text-success">Total Final: R$ {{ number_format($total, 2, ',', '.') }}</h4>
                                <small class="text-danger" id="aviso-frete">Selecione um frete para continuar</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- BOTÃO DE CHECKOUT --}}
            <div class="text-end mt-4 mb-5">
                <form action="{{ route('checkout.iniciar') }}" method="POST" id="form-checkout">
                    @csrf
                    {{-- Inputs escondidos que o JS vai preencher --}}
                    <input type="hidden" name="frete_tipo" id="input-frete-tipo">
                    <input type="hidden" name="frete_valor" id="input-frete-valor">
                    
                    <button type="submit" class="btn btn-success btn-lg" id="btn-pagamento" disabled>
                        Selecione o Frete para Continuar
                    </button>
                </form>
            </div>

        @else
            <div class="alert alert-info">
                Seu carrinho está vazio.
            </div>
        @endif
    </div>

    {{-- SCRIPTS (Frete + Cupom + Atualização de Totais) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos do DOM
            const cepInput = document.getElementById('cep-input'); // <--- NOVO
            const btnCalcularFrete = document.getElementById('btn-calcular-frete');
            const containerOpcoesFrete = document.getElementById('opcoes-frete');
            const totalProdutos = {{ $total }};
            const btnPagamento = document.getElementById('btn-pagamento');
            const avisoFrete = document.getElementById('aviso-frete');
            
            const btnAplicarCupom = document.getElementById('btn-aplicar-cupom');
            const btnRemoverCupom = document.getElementById('btn-remover-cupom');
            const containerCupomSucesso = document.getElementById('cupom-sucesso');
            const containerCupomInput = document.getElementById('grupo-input-cupom');
            const msgErroCupom = document.getElementById('cupom-erro');

            let descontoAtual = 0;
            let freteAtual = 0;

            function atualizarTotalGeral() {
                let totalFinal = totalProdutos + freteAtual - descontoAtual;
                if (totalFinal < 0) totalFinal = 0;
                document.getElementById('total-final').innerText = 'Total Final: R$ ' + totalFinal.toFixed(2).replace('.', ',');
            }

            // --- MELHORIA DE UX: MÁSCARA DE CEP ---
            cepInput.addEventListener('input', function(e) {
                let value = e.target.value;
                
                // Remove tudo que não é número
                value = value.replace(/\D/g, "");
                
                // Adiciona o traço (00000-000)
                if (value.length > 5) {
                    value = value.replace(/^(\d{5})(\d)/, "$1-$2");
                }
                
                e.target.value = value;
            });

            // --- LÓGICA DE FRETE ---
            btnCalcularFrete.addEventListener('click', function() {
                // Limpa o traço antes de validar/enviar
                const cepRaw = cepInput.value.replace(/\D/g, "");

                if(cepRaw.length !== 8) {
                    alert('Digite um CEP válido (8 números).');
                    return;
                }

                btnCalcularFrete.disabled = true;
                btnCalcularFrete.innerText = '...';
                containerOpcoesFrete.innerHTML = '';

                fetch('/carrinho/calcular-frete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ cep: cepRaw }) // Envia apenas números
                })
                .then(response => response.json())
                .then(data => {
                    btnCalcularFrete.disabled = false;
                    btnCalcularFrete.innerText = 'Calcular';

                    if(data.opcoes.length === 0) {
                        containerOpcoesFrete.innerHTML = '<div class="alert alert-warning">Nenhuma opção encontrada.</div>';
                        return;
                    }

                    data.opcoes.forEach(opcao => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        item.innerHTML = `
                            <div>
                                <strong>${opcao.nome}</strong><br>
                                <small>${opcao.prazo}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">R$ ${opcao.valor.toFixed(2).replace('.', ',')}</span>
                        `;
                        
                        item.addEventListener('click', function() {
                            document.querySelectorAll('#opcoes-frete button').forEach(b => b.classList.remove('active'));
                            this.classList.add('active');

                            freteAtual = opcao.valor;
                            document.getElementById('input-frete-tipo').value = opcao.nome;
                            document.getElementById('input-frete-valor').value = opcao.valor;

                            atualizarTotalGeral();
                            
                            btnPagamento.disabled = false;
                            btnPagamento.innerText = 'Ir para o Pagamento';
                            avisoFrete.style.display = 'none';
                        });

                        containerOpcoesFrete.appendChild(item);
                    });
                })
                .catch(() => {
                    btnCalcularFrete.disabled = false;
                    btnCalcularFrete.innerText = 'Calcular';
                    alert('Erro ao calcular frete.');
                });
            });

            // --- LÓGICA DE CUPOM (Mantida igual) ---
            @if(session('cupom'))
                descontoAtual = {{ session('cupom')['desconto_calculado'] }};
                document.getElementById('valor-desconto-display').innerText = descontoAtual.toFixed(2).replace('.', ',');
                document.getElementById('codigo-cupom-display').innerText = "{{ session('cupom')['codigo'] }}";
                containerCupomInput.style.display = 'none';
                containerCupomSucesso.style.display = 'flex';
                atualizarTotalGeral();
            @endif

            btnAplicarCupom.addEventListener('click', function() {
                const codigo = document.getElementById('cupom-input').value;
                if(!codigo) return;

                btnAplicarCupom.disabled = true;
                msgErroCupom.style.display = 'none';

                fetch('/carrinho/aplicar-cupom', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ codigo: codigo })
                })
                .then(res => res.json().then(data => ({status: res.status, body: data})))
                .then(res => {
                    btnAplicarCupom.disabled = false;
                    if(res.status !== 200) {
                        msgErroCupom.innerText = res.body.erro;
                        msgErroCupom.style.display = 'block';
                        return;
                    }
                    descontoAtual = parseFloat(res.body.desconto);
                    document.getElementById('valor-desconto-display').innerText = descontoAtual.toFixed(2).replace('.', ',');
                    document.getElementById('codigo-cupom-display').innerText = res.body.codigo;
                    containerCupomInput.style.display = 'none';
                    containerCupomSucesso.style.display = 'flex';
                    atualizarTotalGeral();
                });
            });

            btnRemoverCupom.addEventListener('click', function() {
                fetch('/carrinho/remover-cupom', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(() => {
                    descontoAtual = 0;
                    containerCupomInput.style.display = 'flex';
                    containerCupomSucesso.style.display = 'none';
                    document.getElementById('cupom-input').value = '';
                    atualizarTotalGeral();
                });
            });
        });
    </script>
@endsection