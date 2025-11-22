<!DOCTYPE html>
<html>
<head>
    <title>Pedido Recebido</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    
    <h2>Olá, {{ $pedido->user->name }}!</h2>
    
    <p>Recebemos seu pedido <strong>#{{ $pedido->id }}</strong>.</p>
    
    <p>Estamos aguardando a confirmação do pagamento. Você será avisado assim que aprovado.</p>

    <h3>Resumo:</h3>
    <ul>
        @foreach($pedido->produtos as $item)
            <li>
                {{ $item->pivot->quantidade }}x {{ $item->nome }} - 
                R$ {{ number_format($item->pivot->preco, 2, ',', '.') }}
            </li>
        @endforeach
    </ul>

    <p><strong>Total: R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong></p>

    <hr>
    <p>Obrigado por comprar na WNNB Skate Shop!</p>
</body>
</html>