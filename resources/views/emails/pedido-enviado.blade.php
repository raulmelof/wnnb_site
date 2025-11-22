<!DOCTYPE html>
<html>
<body>
    <h2>Ótimas notícias, {{ $pedido->user->name }}!</h2>
    <p>Seu pedido <strong>#{{ $pedido->id }}</strong> foi embalado e despachado.</p>

    @if($pedido->codigo_rastreio)
        <div style="padding: 15px; background: #f3f4f6; border: 1px solid #ddd; margin: 20px 0;">
            <strong>Código de Rastreio:</strong>
            <h3 style="margin: 5px 0;">{{ $pedido->codigo_rastreio }}</h3>
        </div>
    @endif

    <p>Você pode acompanhar a entrega no site dos Correios ou da transportadora.</p>
    <hr>
    <p>Equipe WNNB Skate Shop</p>
</body>
</html>