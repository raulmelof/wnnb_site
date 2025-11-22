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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('endereco_cep', 9)->nullable()->after('status');
            $table->string('endereco_rua')->nullable()->after('endereco_cep');
            $table->string('endereco_numero', 20)->nullable()->after('endereco_rua');
            $table->string('endereco_complemento')->nullable()->after('endereco_numero');
            $table->string('endereco_bairro')->nullable()->after('endereco_complemento');
            $table->string('endereco_cidade')->nullable()->after('endereco_bairro');
            $table->string('endereco_estado', 2)->nullable()->after('endereco_cidade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'endereco_cep', 'endereco_rua', 'endereco_numero', 
                'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_estado'
            ]);
        });
    }
};
