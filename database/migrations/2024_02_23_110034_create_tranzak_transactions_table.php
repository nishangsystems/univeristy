<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranzakTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tranzak_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 25);
            $table->integer('amount');
            $table->string('currency_code', 12)->nullable();
            $table->string('purpose', 32)->nullable();
            $table->string('mobile_wallet_number', 32);
            $table->string('transaction_ref', 32);
            $table->string('app_id', 25);
            $table->string('transaction_id', 32);
            $table->timestamp('transaction_time')->useCurrent();
            $table->string('payemt_method', 64);
            $table->integer('payer_user_id');
            $table->string('payer_name')->nullable();
            $table->string('payer_account_id');
            $table->integer('merchant_fee');
            $table->string('merchant_account_id')->nullable();
            $table->integer('net_amount_recieved');
            $table->boolean('submitted')->default(0);
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
        Schema::dropIfExists('tranzak_transactions');
    }
}
