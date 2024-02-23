<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_stock', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_id');
            $table->integer('student_id');
            $table->integer('quantity')->nullable();
            $table->integer('type', ['receivable', 'givable'])->default('givable');
            $table->integer('campus_id')->nullable();
            $table->integer('year_id')->nullable();
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
        Schema::dropIfExists('student_stock');
    }
}
