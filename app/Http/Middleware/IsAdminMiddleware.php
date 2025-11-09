<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o usuário está autenticado E se o seu nivel_acesso é 'admin'
        if (auth()->check() && auth()->user()->nivel_acesso == 'admin') {
            // Se for admin, permite que a requisição continue
            return $next($request);
        }

        // Se não for admin, redireciona para a página inicial com uma mensagem de erro
        return redirect('/')->with('error', 'Acesso negado. Você não tem permissão para acessar esta página.');
    }
}