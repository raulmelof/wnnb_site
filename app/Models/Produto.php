<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'imagem',
        'imagemHover',
        'categoria',
    ];

    /**
     * Define que um Produto TEM MUITAS Variações (tamanhos/estoque).
     */
    public function variacoes()
    {
        return $this->hasMany(ProdutoVariacao::class);
    }

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_produtos');
    }
}