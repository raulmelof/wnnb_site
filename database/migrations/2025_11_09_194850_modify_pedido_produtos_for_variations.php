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
        Schema::table('pedido_produtos', function (Blueprint $table) {
            // Adiciona a nova coluna para a ID da variação
            $table->foreignId('produto_variacao_id')
                  ->nullable() // Permite nulo por enquanto
                  ->after('produto_id')
                  ->constrained('produto_variacoes'); // Aponta para a nossa nova tabela

            // Torna a 'produto_id' original nula, pois a 'variacao_id' é que manda
            // Precisamos do doctrine/dbal para isso
            $table->foreignId('produto_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedido_produtos', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna
            $table->dropForeign(['produto_variacao_id']);
            $table->dropColumn('produto_variacao_id');

            // Reverte a 'produto_id' para não-nula
            $table->foreignId('produto_id')->nullable(false)->change();
        });
    }
};
