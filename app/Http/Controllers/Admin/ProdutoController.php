<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produtos = Produto::all();
        return view('admin.produtos.index', ['produtos' => $produtos]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric',
            'descricao' => 'nullable|string',
            'categoria' => 'required|string|max:50',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $produto = new Produto($request->all());

        // Lógica para upload da imagem
        if ($request->hasFile('imagem')) {
            // Salva a imagem em 'storage/app/public/imagens' e guarda o caminho no banco
            $caminhoImagem = $request->file('imagem')->store('imagens', 'public');
            $produto->imagem = $caminhoImagem;
        }

        $produto->save();

        return redirect()->route('admin.produtos.index')->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Produto $produto)
    {
        // O Laravel já encontrou o produto pelo ID na URL.
        // Apenas retornamos a view de edição, passando o produto encontrado.
        return view('admin.produtos.edit', ['produto' => $produto]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric',
            'descricao' => 'nullable|string',
            'categoria' => 'required|string|max:50',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $dados = $request->except('imagem');

        // Se uma nova imagem foi enviada
        if ($request->hasFile('imagem')) {
            // Apaga a imagem antiga para não ocupar espaço
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }
            // Salva a nova imagem
            $caminhoImagem = $request->file('imagem')->store('imagens', 'public');
            $dados['imagem'] = $caminhoImagem;
        }

        $produto->update($dados);

        return redirect()->route('admin.produtos.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produto $produto)
    {
        // Apaga a imagem associada ao produto do disco
        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }
        
        // Apaga o registro do produto do banco de dados
        $produto->delete();

        return redirect()->route('admin.produtos.index')->with('success', 'Produto excluído com sucesso!');
    }
}
