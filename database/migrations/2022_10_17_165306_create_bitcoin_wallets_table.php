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
        /**
         *  FF Wallets
         */
        Schema::create('bitcoin_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('combined_order_id');
            $table->string('invoice_id');
            $table->string('invoice');
            $table->string('token');
            $table->string('btc_amount');
            $table->enum('status', ['pending', 'paid', 'expired'])->default('pending');
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
        Schema::dropIfExists('bitcoin_wallets');
    }
};
