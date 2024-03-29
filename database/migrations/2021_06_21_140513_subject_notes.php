<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SubjectNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_path');
            $table->string('note_name');
            $table->integer('batch_id')->unsigned();
            $table->integer('class_subject_id')->unsigned();
            $table->tinyInteger('status');
            $table->string('type');
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
        Schema::dropIfExists('subject_notes');
    }
}
