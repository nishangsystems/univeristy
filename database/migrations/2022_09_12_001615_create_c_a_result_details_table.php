<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCAResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_a_result_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('c_a_result_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('mark', 5, 2);
            $table->timestamps();

            $table->foreign('c_a_result_id')->references('id')->on('c_a_results');
            $table->foreign('student_id')->references('id')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_a_result_details');
    }
}
