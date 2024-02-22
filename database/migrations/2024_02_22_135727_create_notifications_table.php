<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 256);
            $table->integer('school_unit_id')->nullable();
            $table->integer('unit_id');
            $table->text('message');
            $table->boolean('status')->default(1);
            $table->integer('level_id')->nullable();
            $table->integer('campus_id')->nullable();
            $table->integer('user_id');
            $table->date('date');
            $table->enum('visibility', ['students', 'teachers', 'admins', 'general'])->default('students');
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
        Schema::dropIfExists('notifications');
    }
}
