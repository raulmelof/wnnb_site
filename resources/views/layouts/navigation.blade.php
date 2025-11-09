<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        {{-- Logo --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('storage/imagens/logo.jpg') }}" alt="WNB Logo" style="height: 40px;">
        </a>

        {{-- Botão "Hamburguer" --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            {{-- Links principais à esquerda --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('produtos.por_categoria', 'Camisetas') }}">Camisetas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('produtos.por_categoria', 'Calças') }}">Calças</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('produtos.por_categoria', 'Moletons') }}">Moletons</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('produtos.por_categoria', 'Shapes') }}">Shapes</a>
                </li>
            </ul>

            <form class="d-flex" action="{{ route('produtos.buscar') }}" method="GET">
                <input class="form-control me-2" type="search" name="termo" placeholder="Buscar..." aria-label="Buscar" required>
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            {{-- Ícones e Lógica de Autenticação à direita --}}
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                
                {{-- ÍCONE DO CARRINHO (SEMPRE VISÍVEL) --}}
                <li class="nav-item">
                    <a href="{{ route('cart.index') }}" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="badge rounded-pill bg-danger">
                                {{-- Substitua a linha 'count' pela linha 'array_sum' abaixo --}}
                                {{ array_sum(array_column(session('cart'), 'quantidade')) }}
                            </span>
                        @endif
                    </a>
                </li>

                @auth
                    {{-- Dropdown do usuário LOGADO --}}

                    @if(Auth::user()->nivel_acesso == 'admin')
                        <li class="nav-item">
                            {{-- Adicionei classes do Bootstrap para destacar o link --}}
                            <a class="nav-link fw-bold text-danger" href="{{ route('admin.produtos.index') }}">
                                Gerenciar Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="{{ route('admin.pedidos.index') }}">
                                Gerenciar Pedidos
                            </a>
                        </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                Perfil
                            </a>
                            <a class="dropdown-item" href="{{ route('meus-pedidos.index') }}">
                                Meus Pedidos
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    Sair
                                </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @else
                    {{-- Links para VISITANTES (não logados) --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Log in</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</nav>