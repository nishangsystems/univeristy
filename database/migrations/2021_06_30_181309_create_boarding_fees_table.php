<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardingFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boarding_fees', function (Blueprint $table) {
            $table->id();
            $table->integer('amount_new_student');
            $table->integer('amount_old_student');
            $table->integer('boarding_typw');
            $table->integer('parent_id');
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
        Schema::dropIfExists('boarding_fees');
    }
}
