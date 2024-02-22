<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('campus_id')->nullable();
            $table->integer('user_id');
            $table->integer('course_id');
            $table->string('title');
            $table->text('message');
            $table->date('date');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('course_notifications');
    }
}
