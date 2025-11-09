<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        // Validação dos dados principais e das variações
        $request->validate([
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variacoes' => 'required|array|min:1', // Deve ter pelo menos uma variação
            'variacoes.*.tamanho' => 'required|string|max:50', // Valida cada item do array
            'variacoes.*.estoque' => 'required|integer|min:0',
        ]);

        $data = $request->except('imagem', 'variacoes'); // Pega dados do produto

        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('imagens', 'public');
            $data['imagem'] = $path;
        }

        // Usamos uma transação para garantir que o produto e suas variações
        // sejam criados com sucesso, ou nada seja salvo.
        DB::transaction(function () use ($data, $request) {
            // 1. Cria o produto principal
            $produto = Produto::create($data);

            // 2. Anexa as variações ao produto
            foreach ($request->variacoes as $variacao) {
                $produto->variacoes()->create([
                    'tamanho' => $variacao['tamanho'],
                    'estoque' => $variacao['estoque'],
                ]);
            }
        });

        return redirect()->route('admin.produtos.index')
                         ->with('success', 'Produto e suas variações criados com sucesso.');
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
        // Validação similar ao 'store'
        $request->validate([
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variacoes' => 'required|array|min:1',
            'variacoes.*.tamanho' => 'required|string|max:50',
            'variacoes.*.estoque' => 'required|integer|min:0',
        ]);

        $data = $request->except('imagem', 'variacoes');

        if ($request->hasFile('imagem')) {
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }
            $path = $request->file('imagem')->store('imagens', 'public');
            $data['imagem'] = $path;
        }

        // Transação para segurança
        DB::transaction(function () use ($produto, $data, $request) {
            // 1. Atualiza os dados do produto principal
            $produto->update($data);

            // 2. Sincroniza as Variações (o jeito complexo, mas correto)
            $idsDasVariacoesDoForm = [];

            foreach ($request->variacoes as $variacaoData) {
                // Se a variação já tem ID, atualiza. Senão, cria.
                $variacao = $produto->variacoes()->updateOrCreate(
                    [
                        'id' => $variacaoData['id'] ?? null // Procura pelo ID
                    ],
                    [
                        'tamanho' => $variacaoData['tamanho'], // Dados para atualizar/criar
                        'estoque' => $variacaoData['estoque'],
                    ]
                );
                $idsDasVariacoesDoForm[] = $variacao->id; // Guarda o ID
            }

            // 3. Deleta variações antigas
            // Se alguma variação que estava no banco não veio no formulário, ela é deletada.
            $produto->variacoes()->whereNotIn('id', $idsDasVariacoesDoForm)->delete();
        });


        return redirect()->route('admin.produtos.index')
                         ->with('success', 'Produto e suas variações atualizados com sucesso.');
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
