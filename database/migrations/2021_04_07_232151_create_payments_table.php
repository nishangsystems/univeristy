<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_id');
            $table->integer('debt')->default(0);
            $table->integer('student_id');
            $table->integer('batch_id');
            $table->integer('unit_id');
            $table->integer('amount');
            $table->integer('payment_year_id')->nullable();
            $table->string('import_reference', 50);
            $table->string('reference_number', 50)->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('paid_id')->nullable();
            $table->integer('transaction_id')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
