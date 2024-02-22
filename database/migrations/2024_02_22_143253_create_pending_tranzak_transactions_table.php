<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingTranzakTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_tranzak_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 25);
            $table->integer('amount');
            $table->string('currency_code', 12)->nullable();
            $table->string('transaction_ref', 32);
            $table->string('app_id', 25);
            $table->string('description', 256);
            $table->timestamp('transaction_time')->useCurrent();
            $table->string('payment_type', 64)->nullable();
            $table->string('user_type', 32)->nullable();
            $table->integer('payment_id');
            $table->integer('student_id');
            $table->integer('batch_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('original_amount')->nullable();
            $table->string('reference_number', 64)->nullable();
            $table->string('paid_by', 32)->nullable();
            $table->string('purpose', 32)->nullable();
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
        Schema::dropIfExists('pending_tranzak_transactions');
    }
}
