<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PayIncomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_incomes', function (Blueprint $table) {
            $table->id();
            $table->integer('income_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->integer('amount')->nullable();
            $table->integer('batch_id');
            $table->integer('class_id');
            $table->integer('user_id')->nullable();
            $table->integer('payed_by');
            $table->integer('paid_by')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->boolean('cash')->default(0);
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
        Schema::dropIfExists('payment_incomes');
    }
}
