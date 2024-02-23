<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->integer('teacher_subject_id')->nullable();
            $table->integer('subject_id');
            $table->text('title');
            $table->integer('duration')->nullable();
            $table->enum('level', ['1', '2']);
            $table->integer('parent_id');
            $table->integer('campus_id')->nullable();
            $table->integer('week')->nullable();
            $table->integer('teacher_id')->nullable();
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
        Schema::dropIfExists('topics');
    }
}
