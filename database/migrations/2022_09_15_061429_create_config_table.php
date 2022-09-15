<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->unsignedBigInteger('degree_id')->nullable();
            $table->unsignedBigInteger('degree_semester_id')->nullable();
            $table->unsignedBigInteger('sequence_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('batches');
            $table->foreign('school_id')->references('id')->on('school');
            $table->foreign('campus_id')->references('id')->on('campuses');
            $table->foreign('degree_id')->references('id')->on('degrees');
            $table->foreign('degree_semester_id')->references('id')->on('degree_semesters');
            $table->foreign('sequence_id')->references('id')->on('sequence');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config');
    }
}
