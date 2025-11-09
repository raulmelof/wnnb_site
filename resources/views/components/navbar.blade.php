<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        {{-- Logo --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            {{-- Lembre-se de colocar sua logo em storage/app/public/imagens/logo.jpg --}}
            <img src="{{ asset('storage/imagens/logo.jpg') }}" alt="WNB Logo" style="height: 40px;">
        </a>

        {{-- Botão "Hamburguer" para telas pequenas --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Links da Navbar --}}
        <div class="collapse navbar-collapse" id="main-navbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#">Camisetas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Calças</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Moletons</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Shapes</a>
                </li>
            </ul>

            {{-- Ícones e busca à direita --}}
            <div class="d-flex align-items-center">
                <form class="d-flex me-3">
                    <input class="form-control me-2" type="search" placeholder="Buscar produtos..." aria-label="Buscar">
                </form>
                {{-- Lógica de Login/Logout virá aqui no futuro --}}
                <a href="#" class="nav-link me-2">
                    <i class="fas fa-user"></i> </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> </a>
            </div>
        </div>
    </div>
</nav>