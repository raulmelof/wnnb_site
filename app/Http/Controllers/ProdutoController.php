<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 1. Pega todos os produtos, já carregando suas variações
        $produtos = Produto::with('variacoes')->get();

        // 2. Verifica o estoque de cada um
        // Estamos adicionando um novo atributo 'total_estoque' ao objeto do produto
        $produtos->each(function ($produto) {
            $produto->total_estoque = $produto->variacoes->sum('estoque');
        });
        
        // 3. Retorna a view 'welcome' e passa os produtos
        return view('welcome', ['produtos' => $produtos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Valida a imagem
        ]);

        $data = $request->except('imagem'); // Pega todos os dados, exceto o arquivo 'imagem'

        if ($request->hasFile('imagem')) {
            // 1. Salva o arquivo na pasta 'storage/app/public/imagens'
            // 2. Retorna o caminho relativo (ex: 'imagens/hash123.jpg')
            $path = $request->file('imagem')->store('imagens', 'public');
            
            // 3. Salva esse caminho no array de dados
            $data['imagem'] = $path;
        }

        Produto::create($data); // Cria o produto com os dados corretos

        return redirect()->route('admin.produtos.index')
                         ->with('success', 'Produto criado com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function show(Produto $produto)
    {
        return view('produto-detalhe', ['produto' => $produto]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function edit(Produto $produto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produto $produto)
    {
        // Validação
        $request->validate([
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->except('imagem');

        if ($request->hasFile('imagem')) {
            // Se uma nova imagem for enviada, apaga a antiga
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }

            // Salva a nova imagem e atualiza o caminho
            $path = $request->file('imagem')->store('imagens', 'public');
            $data['imagem'] = $path;
        }

        // Atualiza o produto com os novos dados
        $produto->update($data);

        return redirect()->route('admin.produtos.index')
                         ->with('success', 'Produto atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produto $produto)
    {
        //
    }

    public function porCategoria(string $categoria)
    {
        // Busca no banco todos os produtos onde a coluna 'categoria'
        // seja igual ao valor recebido na URL.
        $produtos = Produto::where('categoria', $categoria)->get();

        // Retorna uma nova view, passando os produtos filtrados e o nome da categoria
        return view('produtos-por-categoria', [
            'produtos' => $produtos,
            'categoria' => $categoria
        ]);
    }

    public function buscar(Request $request)
    {
        // Valida se o termo de busca foi enviado
        $request->validate([
            'termo' => 'required|string|max:50'
        ]);

        $termo = $request->input('termo');

        // Busca no banco de dados por produtos onde o nome seja 'parecido' com o termo buscado
        $produtos = Produto::where('nome', 'LIKE', '%' . $termo . '%')
                           ->orWhere('descricao', 'LIKE', '%' . $termo . '%')
                           ->get();

        // Retorna a view de resultados, passando os produtos encontrados e o termo buscado
        return view('busca-resultados', [
            'produtos' => $produtos,
            'termo' => $termo
        ]);
    }
}
