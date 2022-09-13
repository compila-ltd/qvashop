<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_queries', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('seller_id')->nullable();
            $table->integer('product_id');
            $table->longText('question');
            $table->longText('reply')->nullable();
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
        Schema::dropIfExists('product_queries');
    }
}
