<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoVariacao;
use Illuminate\Http\Request;
use App\Services\CorreiosService;

class CartController extends Controller
{
    /**
     * Mostra a página do carrinho.
     */
    public function index()
    {
        $cart = session('cart', []);
        
        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['preco'] * $details['quantidade'];
        }

        return view('carrinho', ['cart' => $cart, 'total' => $total]);
    }

    /**
     * Adiciona uma VARIAÇÃO de produto ao carrinho.
     */
    public function add(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'variacao_id' => 'required|exists:produto_variacoes,id',
        ]);

        $produto = Produto::find($request->produto_id);
        $variacao = ProdutoVariacao::find($request->variacao_id);

        // Garante que a variação pertence ao produto (segurança)
        if ($variacao->produto_id != $produto->id) {
            return redirect()->back()->with('error', 'Erro: A variação não pertence a este produto.');
        }

        // Pega o carrinho da sessão
        $cart = $request->session()->get('cart', []);
        
        // A ID única no carrinho agora é a ID DA VARIAÇÃO
        $idDaVariacao = $variacao->id;

        // Verifica se o item já está no carrinho
        if (isset($cart[$idDaVariacao])) {
            $novaQuantidade = $cart[$idDaVariacao]['quantidade'] + 1;
        } else {
            $novaQuantidade = 1;
        }

        // --- VERIFICAÇÃO DE ESTOQUE ---
        if ($novaQuantidade > $variacao->estoque) {
            return redirect()->back()->with('error', 'Desculpe, não há estoque suficiente para este item.');
        }

        // Adiciona ou atualiza o item no carrinho
        $cart[$idDaVariacao] = [
            "produto_id" => $produto->id,
            "nome" => $produto->nome,
            "tamanho" => $variacao->tamanho, // Guarda o tamanho
            "preco" => $produto->preco, // O preço ainda vem do produto "pai"
            "imagem" => $produto->imagem,
            "quantidade" => $novaQuantidade,
        ];

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produto adicionado ao carrinho!');
    }

    /**
     * Atualiza a quantidade de uma VARIAÇÃO no carrinho.
     */
    public function update(Request $request, $variacao_id)
    {
        $cart = $request->session()->get('cart');

        $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);
        
        // Verifica se o item existe no carrinho
        if (isset($cart[$variacao_id])) {
            
            // --- VERIFICAÇÃO DE ESTOQUE ---
            $variacao = ProdutoVariacao::find($variacao_id);
            if ($request->quantidade > $variacao->estoque) {
                return redirect()->route('cart.index')->with('error', 'Desculpe, não há estoque suficiente para esta quantidade.');
            }

            // Atualiza a quantidade
            $cart[$variacao_id]['quantidade'] = $request->quantidade;
            $request->session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('success', 'Quantidade atualizada!');
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    /**
     * Remove uma VARIAÇÃO do carrinho.
     */
    public function remove(Request $request, $variacao_id)
    {
        $cart = $request->session()->get('cart');

        if(isset($cart[$variacao_id])) {
            unset($cart[$variacao_id]);
            $request->session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Produto removido do carrinho.');
    }

    /**
     * Calcula o frete (Correios Manual + Regra de Guarulhos)
     */
    public function calcularFrete(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|size:8',
        ]);

        $cepDestino = $request->cep;
        $cepOrigem = '07072010'; // Guarulhos (Exemplo)

        $cart = session('cart', []);

        if (empty($cart)) {
            return response()->json(['erro' => 'Carrinho vazio.'], 400);
        }

        // --- 1. PREPARAR O PACOTE ---
        $pesoTotal = 0;
        $alturaTotal = 0;
        $larguraMax = 0;
        $comprimentoMax = 0;

        foreach ($cart as $item) {
            // Precisamos pegar o produto pai para ver as dimensões
            // Como salvamos 'produto_id' no carrinho, podemos usar:
            $produto = Produto::find($item['produto_id']);
            
            $pesoItem = $produto->peso ?? 0.300;
            $alturaItem = $produto->altura ?? 5;
            $larguraItem = $produto->largura ?? 20;
            $comprimentoItem = $produto->comprimento ?? 20;

            $quantidade = $item['quantidade'];

            $pesoTotal += $pesoItem * $quantidade;
            $alturaTotal += $alturaItem * $quantidade; // Empilha alturas
            
            if ($larguraItem > $larguraMax) $larguraMax = $larguraItem;
            if ($comprimentoItem > $comprimentoMax) $comprimentoMax = $comprimentoItem;
        }

        $opcoesDeFrete = [];

        // --- 2. CONSULTAR CORREIOS (Via Nosso Service) ---
        try {
            $correiosService = new CorreiosService();
            $opcoesDeFrete = $correiosService->calcular(
                $cepOrigem,
                $cepDestino,
                $pesoTotal,
                $alturaTotal,
                $larguraMax,
                $comprimentoMax
            );

        } catch (\Exception $e) {
            // Falha silenciosa
        }

        // --- 3. REGRA DE GUARULHOS (ViaCEP) ---
        try {
            $response = Http::get("https://viacep.com.br/ws/{$cepDestino}/json/");
            $dadosEndereco = $response->json();

            // Se for Guarulhos, adiciona a opção Local no INÍCIO do array
            if (isset($dadosEndereco['localidade']) && $dadosEndereco['localidade'] === 'Guarulhos') {
                array_unshift($opcoesDeFrete, [
                    'nome' => 'A Combinar / Uber Flash / Retirada',
                    'valor' => 0.00,
                    'prazo' => 'Combinar após pagamento',
                    'codigo' => 'local'
                ]);
            }

        } catch (\Exception $e) {
            // Falha silenciosa
        }

        return response()->json([
            'opcoes' => $opcoesDeFrete
        ]);
    }
}