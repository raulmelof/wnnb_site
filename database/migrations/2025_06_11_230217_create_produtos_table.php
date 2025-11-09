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
    // Em ..._create_produtos_table.php
    // Em ..._create_produtos_table.php
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->decimal('preco', 10, 2); // Ajustado para corresponder ao seu DECIMAL(10,2)
            $table->string('imagem')->nullable();
            $table->string('imagemHover')->nullable(); // Adicionando o campo que faltava
            $table->string('categoria', 50);      // Campo de texto para categoria, como no seu original
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos');
    }
};
