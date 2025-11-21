<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProdutoController as AdminProdutoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===== NOSSAS ROTAS DA LOJA =====
// Rota principal agora aponta para o controller, corrigindo o erro
Route::get('/', [ProdutoController::class, 'index'])->name('home');

// Rota de detalhes do produto
Route::get('/produto/{produto}', [ProdutoController::class, 'show'])->name('produto.show');

Route::get('/categoria/{categoria}', [ProdutoController::class, 'porCategoria'])->name('produtos.por_categoria');

Route::get('/buscar', [ProdutoController::class, 'buscar'])->name('produtos.buscar');


// Rotas para editar o perfil do usuário
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
    Route::post('/carrinho/adicionar', [CartController::class, 'add'])->name('cart.add');
    Route::post('/carrinho/remover/{variacao_id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/carrinho/atualizar/{variacao_id}', [CartController::class, 'update'])->name('cart.update');

    Route::prefix('meus-pedidos')->name('meus-pedidos.')->group(function () {
        Route::get('/', [PedidoController::class, 'index'])->name('index');
        Route::get('/{pedido}', [PedidoController::class, 'show'])->name('show');
    });

    // Rota que o botão "Finalizar Compra" do carrinho irá chamar
    Route::post('/checkout/iniciar', [CheckoutController::class, 'iniciarPagamento'])
         ->name('checkout.iniciar');

    // Rota de callback (a 'redirect_url' que configuramos na InfinitePay)
    // Usamos GET pois o usuário é redirecionado pelo navegador
    Route::get('/checkout/callback', [CheckoutController::class, 'processarCallback'])
         ->name('checkout.callback');

    // Página de sucesso para onde o usuário é enviado após a verificação
    Route::get('/pedido/sucesso/{pedido}', [CheckoutController::class, 'mostrarSucesso'])
         ->name('checkout.sucesso');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // A rota resource cria todas as 7 rotas do CRUD de uma vez
    Route::resource('produtos', AdminProdutoController::class);

    Route::get('/pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');

    Route::get('/pedidos/{pedido}', [AdminPedidoController::class, 'show'])->name('pedidos.show');
    Route::put('/pedidos/{pedido}', [AdminPedidoController::class, 'update'])->name('pedidos.update');

    Route::resource('cupons', App\Http\Controllers\Admin\CupomController::class);
});

// Arquivo que contém as rotas de login, registro, logout, etc.
require __DIR__.'/auth.php';