<!DOCTYPE html>
<html>
<head>
    <title>Pagamento Aprovado</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    
    <h2 style="color: #28a745;">Pagamento Confirmado!</h2>
    
    <p>Olá, {{ $pedido->user->name }}.</p>
    
    <p>Temos ótimas notícias! O pagamento do seu pedido <strong>#{{ $pedido->id }}</strong> foi aprovado pela InfinitePay.</p>
    
    <div style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; margin: 20px 0;">
        <p><strong>Método:</strong> {{ ucfirst($pedido->payment_method) }}</p>
        <p><strong>Total:</strong> R$ {{ number_format($pedido->total, 2, ',', '.') }}</p>
        
        @if($pedido->receipt_url)
            <p><a href="{{ $pedido->receipt_url }}">Ver Comprovante</a></p>
        @endif
    </div>

    <p>Agora vamos separar seus produtos e prepará-los para envio.</p>
    <p>Você receberá outro e-mail assim que o pacote for despachado.</p>

    <hr>
    <p>Equipe WNNB Skate Shop</p>
</body>
</html>