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
        Schema::table('order_details', function (Blueprint $table) {
            $table->double('price_cup', 20, 2)->default(0.00)->after('price');
            $table->double('price_mlc', 20, 2)->default(0.00)->after('price');
            $table->double('shipping_cost_cup', 20, 2)->default(0.00)->after('shipping_cost');
            $table->double('shipping_cost_mlc', 20, 2)->default(0.00)->after('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('price_cup');
            $table->dropColumn('price_mlc');
            $table->dropColumn('shipping_cost_cup');
            $table->dropColumn('shipping_cost_mlc');
        });
    }
};
