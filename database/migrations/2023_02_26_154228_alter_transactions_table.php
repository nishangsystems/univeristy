<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('student_id');
            $table->integer('semester_id')->nullable();
            $table->integer('amount');
            $table->integer('year_id');
            $table->string('reference');
            $table->enum('status',['pending', 'completed', 'failed']);
            $table->string('tel', 12);
            $table->string('payment_method')->nullable();
            $table->text('payment_purpose')->nullable();
            $table->integer('transaction_id');
            $table->integer('payment_id');
            $table->string('financialTransactionId', 64);
            $table->boolean('is_charges')->default(0);
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
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->dropColumn(['status','payment_method','transaction_id','payment_purpose']);
        });
    }
}
