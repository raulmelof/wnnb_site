{{-- Este componente não precisa de parâmetros. Ele usa uma função do Laravel
     para gerar a URL da página anterior que o usuário visitou. --}}
<a href="{{ url()->previous() }}" class="btn btn-secondary mt-4">
    &larr; Voltar
</a>