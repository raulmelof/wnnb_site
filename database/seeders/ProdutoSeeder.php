<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importante adicionar esta linha

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('produtos')->insert([
            [
                'nome' => 'Camiseta Wnnb-v1',
                'preco' => 89.90,
                'imagem' => 'imagens/camiseta1.jpg',
                'imagemHover' => 'imagens/camiseta1costas.jpg',
                'categoria' => 'Camisetas',
                'descricao' => 'Camiseta de algodão de alta qualidade, leve e confortável.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Camiseta Wnnb-v2',
                'preco' => 99.90,
                'imagem' => 'imagens/camiseta2.jpg',
                'imagemHover' => null,
                'categoria' => 'Camisetas',
                'descricao' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Shape Wnnb-v1',
                'preco' => 159.90,
                'imagem' => 'imagens/shape1.jpg',
                'imagemHover' => null,
                'categoria' => 'shapes',
                'descricao' => 'Shape de skate feito em maple canadense, resistente e durável.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Shape Wnnb-v2',
                'preco' => 159.90,
                'imagem' => 'imagens/shape2.jpg',
                'imagemHover' => null,
                'categoria' => 'shapes',
                'descricao' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Calça Wnnb-v1',
                'preco' => 129.90,
                'imagem' => 'imagens/calca1.jpg',
                'imagemHover' => null,
                'categoria' => 'calças',
                'descricao' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Calça Wnnb-v2',
                'preco' => 129.90,
                'imagem' => 'imagens/calca2.jpg',
                'imagemHover' => null,
                'categoria' => 'calças',
                'descricao' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Moletom Wnnb-v1',
                'preco' => 139.90,
                'imagem' => 'imagens/moletom1.jpg',
                'imagemHover' => null,
                'categoria' => 'moletons',
                'descricao' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Moletom Wnnb-v2',
                'preco' => 139.90,
                'imagem' => 'imagens/moletom2.jpg',
                'imagemHover' => null,
                'categoria' => 'moletons',
                'descricao' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}