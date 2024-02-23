<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
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
            $table->integer('batch_id');
            $table->integer('student_id');
            $table->integer('class_id');
            $table->integer('semester_id');
            $table->string('sequence');
            $table->integer('subject_id');
            $table->double('ca_score');
            $table->double('exam_score')->nullable();
            $table->double('coef');
            $table->string('remark');
            $table->string('reference');
            $table->integer('class_subject_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('campus_id');
            $table->boolean('published')->default(1);
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
        Schema::dropIfExists('results');
    }
}
