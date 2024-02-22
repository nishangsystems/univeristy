<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grading', function (Blueprint $table) {
            $table->id();
            $table->decimal('lower', 4, 2)->nullable();
            $table->decimal('upper', 4, 2)->nullable();
            $table->decimal('weight', 4, 2)->nullable();
            $table->string('grade', 4)->nullable();
            $table->integer('status')->nullable();
            $table->string('remark')->nullable();
            $table->integer('grading_type_id')->nullable();
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
        Schema::dropIfExists('grading');
    }
}
