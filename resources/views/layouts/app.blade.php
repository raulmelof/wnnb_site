<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>WNB - @yield('title', 'Sua Loja de Skate')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Adiciona um espaçamento no topo do corpo para compensar a navbar fixa */
        body {
            background-color: #f8f9fa; /* Um cinza claro para o fundo */

            #fullbanner {
                position: relative;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
            }

            #fullbanner img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            #footer {
                background-color: black;
                color: white;
                padding: 0px 0px;
                font-size: 14px;
                display: flex;
                justify-content: space-around;
                align-items: center;
                flex-wrap: wrap;
                text-align: center;
                margin-top: 5vh;
            }

            .footer-section {
                flex: 1;
                min-width: 250px;
                margin: 10px;
            }

            .footer-section h3 {
                margin-bottom: 10px;
                font-size: 18px;
            }

            .footer-section img {
                width: 40px;
                margin: 5px;
            }

            .footer-section p {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                margin-bottom: 0.8vh;
            }

            .footer-section p img {
                width: 20px;
            }
        }
    </style>
</head>
<body class="antialiased">
    {{-- A navbar só será exibida aqui se a variável $hideNavbar não estiver definida --}}
    @if(!isset($hideNavbar))
        @include('layouts.navigation')
    @endif

    <main>
        {{-- Adicione este bloco para checar e exibir mensagens da sessão --}}
        @if(session('success'))
            <x-alerta type="success" :message="session('success')" />
        @endif
        
        @if(session('error'))
            <x-alerta type="danger" :message="session('error')" />
        @endif

        @yield('content')
    </main>

    <x-footer />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>