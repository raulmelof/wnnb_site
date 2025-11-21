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
        Schema::create('cupons', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // O código que o cliente digita (ex: "BEMVINDO10")
            
            // Tipo de desconto:
            // 'percentual' = 10% de desconto
            // 'fixo' = R$ 10,00 de desconto
            $table->enum('tipo', ['percentual', 'fixo']);
            
            $table->decimal('valor', 10, 2); // O valor (10.00)
            
            // Regras de Validade (Opcionais)
            $table->date('validade')->nullable(); // Data de expiração
            $table->integer('limite_uso')->nullable(); // Máximo de vezes que pode ser usado no total
            $table->integer('usos_atuais')->default(0); // Contador de usos
            $table->boolean('ativo')->default(true); // Botão de liga/desliga manual
            
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
        Schema::dropIfExists('cupons');
    }
};
