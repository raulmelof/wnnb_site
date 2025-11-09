{{-- Usamos a função alertClass() para definir a cor do alerta dinamicamente --}}
<div class="{{ $alertClass() }}" role="alert">
    {{ $message }}
</div>