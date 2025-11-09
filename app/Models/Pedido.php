<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Define quais campos podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'total',
        'status',
    ];

    // Define que um Pedido PERTENCE A um Usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define que um Pedido PERTENCE A MUITOS Produtos (através da tabela pivot 'pedido_produtos')
    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'pedido_produtos')->withPivot('quantidade', 'preco');
    }
}