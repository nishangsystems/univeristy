<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Promotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('promotions');
        Schema::create('promotions', function(Blueprint $table){
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('from_year');
            $table->string('to_year');
            $table->string('from_class');
            $table->string('to_class');
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
        //
        Schema::dropIfExists('promotions');
    }
}
