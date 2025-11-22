<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Etiqueta Pedido #{{ $pedido->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 10cm; /* Largura padrão de etiquetas térmicas ou 1/4 de A4 */
            margin: 0;
            padding: 20px;
            border: 2px dashed #000; /* Borda de corte */
        }
        h1, h3, p { margin: 0 0 10px 0; }
        .destinatario {
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .remetente { font-size: 0.8em; color: #555; }
        .tag {
            background: #000; color: #fff; 
            padding: 5px 10px; font-weight: bold; display: inline-block;
            margin-bottom: 10px;
        }
        @media print {
            body { border: none; } /* Remove borda na impressão real */
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print" style="width:100%; padding: 10px; margin-bottom: 20px; cursor: pointer;">IMPRIMIR ETIQUETA</button>

    <div class="destinatario">
        <span class="tag">DESTINATÁRIO</span>
        <h3>{{ $pedido->user->name }}</h3>
        <p>
            {{ $pedido->endereco_rua }}, {{ $pedido->endereco_numero }} <br>
            @if($pedido->endereco_complemento) {{ $pedido->endereco_complemento }} <br> @endif
            {{ $pedido->endereco_bairro }}
        </p>
        <p>
            <strong>{{ $pedido->endereco_cidade }} / {{ $pedido->endereco_estado }}</strong>
        </p>
        <h3>CEP: {{ $pedido->endereco_cep }}</h3>
    </div>

    <div class="remetente">
        <span class="tag">REMETENTE</span>
        <p><strong>WNNB Skate Shop</strong></p>
        <p>Rua do Seu Estoque, 123</p>
        <p>Guarulhos - SP</p>
        <p>CEP: 07000-000</p>
        <p style="margin-top: 10px;">Pedido: #{{ $pedido->id }}</p>
    </div>
</body>
</html>