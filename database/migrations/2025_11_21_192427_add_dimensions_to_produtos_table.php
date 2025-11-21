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
        Schema::table('produtos', function (Blueprint $table) {
            // Peso em KG (ex: 0.500 para 500g)
            $table->decimal('peso', 8, 3)->nullable()->after('preco');
            
            // Dimensões em CM (inteiros são suficientes para CM)
            $table->integer('altura')->nullable()->after('peso');     // Altura (cm)
            $table->integer('largura')->nullable()->after('altura');  // Largura (cm)
            $table->integer('comprimento')->nullable()->after('largura'); // Comprimento/Profundidade (cm)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['peso', 'altura', 'largura', 'comprimento']);
        });
    }
};
