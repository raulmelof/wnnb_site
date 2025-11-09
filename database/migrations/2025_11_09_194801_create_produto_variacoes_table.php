<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produto_variacoes', function (Blueprint $table) {
            $table->id();
            
            // Chave estrangeira para o produto "pai"
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            
            // O tamanho (ex: "P", "M", "G", "8.5", "Único")
            $table->string('tamanho', 50); 
            
            // O estoque para este tamanho específico
            $table->integer('estoque')->default(0);
            
            $table->timestamps();

            // Garante que não haja tamanhos duplicados para o mesmo produto
            $table->unique(['produto_id', 'tamanho']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_variacoes');
    }
};
