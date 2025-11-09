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
            $table->string('status', 30)->default('aguardando_pagamento')->change();

            $table->string('transaction_id')->nullable()->after('status');
            $table->string('payment_method')->nullable()->after('transaction_id');
            $table->string('receipt_url')->nullable()->after('payment_method');
            $table->string('infinitepay_slug')->nullable()->after('receipt_url');
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
            $table->string('status', 30)->default('pendente')->change();

            $table->dropColumn([
                'transaction_id', 
                'payment_method', 
                'receipt_url', 
                'infinitepay_slug'
            ]);
        });
    }
};
