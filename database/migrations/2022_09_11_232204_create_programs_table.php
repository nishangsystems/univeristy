<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('degree_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('school_semester_id');
            $table->timestamps();

            $table->foreign('degree_id')->references('id')->on('degrees');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('school_semester_id')->references('id')->on('school_semesters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programs');
    }
}
