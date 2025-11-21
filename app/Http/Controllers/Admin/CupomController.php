<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cupom;
use Illuminate\Http\Request;

class CupomController extends Controller
{
    public function index()
    {
        $cupons = Cupom::latest()->get();
        return view('admin.cupons.index', ['cupons' => $cupons]);
    }

    public function create()
    {
        return view('admin.cupons.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'codigo' => 'required|string|unique:cupons|max:20',
            'tipo' => 'required|in:percentual,fixo',
            'valor' => 'required|numeric|min:0',
            'validade' => 'nullable|date',
            'limite_uso' => 'nullable|integer|min:1',
        ]);

        // Checkbox: se não estiver marcado, o request não envia nada.
        // Forçamos true se veio, false se não veio.
        $dados['ativo'] = $request->has('ativo');
        // Converte código para maiúsculas
        $dados['codigo'] = strtoupper($dados['codigo']);

        Cupom::create($dados);

        return redirect()->route('admin.cupons.index')
                         ->with('success', 'Cupom criado com sucesso!');
    }

    public function edit(Cupom $cupom)
    {
        return view('admin.cupons.edit', ['cupom' => $cupom]);
    }

    public function update(Request $request, Cupom $cupom)
    {
        $dados = $request->validate([
            // O 'ignore' serve para não dar erro de "código já existe" se for o próprio cupom
            'codigo' => 'required|string|max:20|unique:cupons,codigo,' . $cupom->id,
            'tipo' => 'required|in:percentual,fixo',
            'valor' => 'required|numeric|min:0',
            'validade' => 'nullable|date',
            'limite_uso' => 'nullable|integer|min:1',
        ]);

        $dados['ativo'] = $request->has('ativo');
        $dados['codigo'] = strtoupper($dados['codigo']);

        $cupom->update($dados);

        return redirect()->route('admin.cupons.index')
                         ->with('success', 'Cupom atualizado!');
    }

    public function destroy(Cupom $cupom)
    {
        $cupom->delete();
        return redirect()->route('admin.cupons.index')
                         ->with('success', 'Cupom removido.');
    }
}