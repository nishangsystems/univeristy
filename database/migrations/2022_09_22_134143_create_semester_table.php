<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemesterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('background_id');
            $table->integer('sem')->nullable();
            $table->boolean('status')->default(0);
            $table->date('ca_upload_latest_date')->nullable();
            $table->date('exam_upload_latest_date')->nullable();
            $table->integer('result_charges')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('courses_min_fee')->nullable();
            $table->float('exam_min_fee')->nullable();
            $table->timestamps();

            $table->foreign('background_id')->references('id')->on('school_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('semester');
    }
}
