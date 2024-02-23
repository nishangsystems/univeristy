<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('deg_name')->nullable();
            $table->integer('unit_id');
            $table->integer('parent_id');
            $table->integer('degree_id')->nullable();
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->integer('base_class')->nullable();
            $table->integer('target_class')->nullable();
            $table->integer('background_id')->default(1);
            $table->integer('grading_type_id');
            $table->integer('max_credit')->nullable();
            $table->integer('ca_total')->nullable();
            $table->integer('exam_total')->nullable();
            $table->integer('resit_cost')->default(2000);
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
        Schema::dropIfExists('school_units');
    }
}
