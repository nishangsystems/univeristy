<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassDelegatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_delegates', function (Blueprint $table) {
            $table->id();
            $table->integer('campus_id');
            $table->integer('class_id');
            $table->integer('student_id');
            $table->integer('year_id');
            $table->boolean('status')->default(1); // tells whether the account is current of disabled.
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
        Schema::dropIfExists('class_delegates');
    }
}
