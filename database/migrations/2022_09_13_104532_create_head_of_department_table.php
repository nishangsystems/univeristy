<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadOfDepartmentTable extends Migration
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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('department_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('campus_id')->references('id')->on('campuses');    
            $table->foreign('user_id')->references('id')->on('users');    
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
