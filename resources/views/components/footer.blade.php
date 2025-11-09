<footer id="footer">
    <div class="footer-section">
        <h3>Métodos de Pagamento</h3>
        {{-- A classe 'payment-methods' não estava no seu CSS, mas adicionei para agrupar as imagens --}}
        <div class="payment-methods">
            {{-- Lembre-se que as imagens devem estar em storage/app/public/imagens/ --}}
            <img src="{{ asset('storage/imagens/visa.jpg') }}" alt="Visa">
            <img src="{{ asset('storage/imagens/mastercard.jpg') }}" alt="Mastercard">
            <img src="{{ asset('storage/imagens/bradesco.jpg') }}" alt="Bradesco">
            <img src="{{ asset('storage/imagens/pix.jpg') }}" alt="Pix">
        </div>
    </div>

    <div class="footer-section">
        <h3>Contato</h3>
        {{-- O seu CSS já estiliza o <p> e o <img> dentro dele, então mantemos a estrutura --}}
        <p>
            <img src="{{ asset('storage/imagens/telefone.jpg') }}" alt="Telefone">
            (11) 99999-9999
        </p>
        <p>
            <img src="{{ asset('storage/imagens/email.jpg') }}" alt="E-mail">
            contato@wannabeskate.com
        </p>
    </div>

    <div class="footer-section">
        <h3>Redes Sociais</h3>
        <a href="https://www.instagram.com/wannabeskateboarding/" target="_blank">
            <img src="{{ asset('storage/imagens/instagram.jpg') }}" alt="Instagram">
        </a>
    </div>
</footer>