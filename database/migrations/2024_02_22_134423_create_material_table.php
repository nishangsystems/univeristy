<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material', function (Blueprint $table) {
            $table->id();
            $table->string('title', '128');
            $table->integer('school_unit_id')->nullable();
            $table->integer('unit_id');
            $table->integer('campus_id')->nullable();
            $table->integer('level_id')->nullable();
            $table->integer('user_id');
            $table->string('file', 256);
            $table->text('message');
            $table->enum('visibility', ['general', 'students', 'teachers', 'admins']);
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
        Schema::dropIfExists('material');
    }
}
