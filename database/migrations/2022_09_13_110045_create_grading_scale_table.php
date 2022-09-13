<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradingScaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grading_scale', function (Blueprint $table) {
            $table->id();
            $table->string('min');
            $table->string('max');
            $table->string('grade');
            $table->string('weight');
            $table->unsignedBigInteger('grading_id');
            $table->string('remark');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('grading_id')->references('id')->on('grading');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grading_scale');
    }
}
