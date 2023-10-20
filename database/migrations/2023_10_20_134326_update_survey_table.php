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
        Schema::table('survey', function (Blueprint $table) {
            $table->string("price_range_favorite")->nullable()->default(NULL)->max(100);
            $table->boolean("free_shipping_favorite")->default(false)->nullable();
            $table->float("rating_favorite")->default(NULL)->nullable()->max(5)->min(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
};
