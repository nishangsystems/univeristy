<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->integer('credit_value')->nullable();
            $table->enum('type', ['GENERAL', 'COMPLUSORY', 'REQURIED'])->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('school_level_id')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs');
            $table->foreign('school_level_id')->references('id')->on('school_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
