<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->decimal('mark');
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('program_course_id');
            $table->unsignedBigInteger('sequence_id');
            $table->unsignedBigInteger('student_id');
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('batch');
            $table->foreign('program_id')->references('id')->on('programs');
            $table->foreign('program_course_id')->references('id')->on('program_courses');
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
        Schema::dropIfExists('exam_result_details');
    }
}
