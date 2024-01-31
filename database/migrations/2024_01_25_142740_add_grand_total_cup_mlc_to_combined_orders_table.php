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
        Schema::table('combined_orders', function (Blueprint $table) {
            $table->double('grand_total_cup', 20, 2)->default(0.00)->after('grand_total');
            $table->double('grand_total_mlc', 20, 2)->default(0.00)->after('grand_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('combined_orders', function (Blueprint $table) {
            $table->dropColumn('grand_total_cup');
            $table->dropColumn('grand_total_mlc');
        });
    }
};
