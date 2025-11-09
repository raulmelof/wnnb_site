<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoVariacao extends Model
{
    use HasFactory;

    /**
     * Informa ao Laravel o nome correto da tabela.
     */
    protected $table = 'produto_variacoes';

    protected $fillable = [
        'produto_id',
        'tamanho',
        'estoque',
    ];

    /**
     * Define que esta variação PERTENCE a um Produto.
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
