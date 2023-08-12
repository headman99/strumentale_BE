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
        Schema::create('result', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            //$table->foreignId("survey")->constrained("survey")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId("item")->constrained("item")->onDelete("cascade")->onUpdate("cascade"); 
            $table->integer("price")->unsigned();
            $table->string("url");
            //$table->unique(["survey, item"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result');
    }
};
