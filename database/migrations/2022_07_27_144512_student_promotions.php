<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StudentPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('student_promotions');
        Schema::create('student_promotions', function(Blueprint $table){
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('student_id');
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
    }
}
