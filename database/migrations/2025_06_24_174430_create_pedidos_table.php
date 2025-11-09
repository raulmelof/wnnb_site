<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Chave estrangeira para o usuário que fez o pedido
            $table->decimal('total', 10, 2);
            $table->string('status', 30)->default('pendente');
            $table->timestamps(); // Cria os campos created_at e updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};