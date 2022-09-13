<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('head_of_department', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('username')->unique();
            $table->enum('gender', ['female','male']);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('department_id');
            $table->rememberToken();
            $table->timestamps();

            
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hod');
    }
}
