<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampusProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campus_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('program_level_id');
            $table->integer('fees')->nullable();
            $table->integer('max_credit')->nullable();
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
        Schema::dropIfExists('campus_programs');
    }
}
