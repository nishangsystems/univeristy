<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->string('name');
            $table->integer('unit');
            $table->string('slug');
            $table->integer('year_id');
            $table->integer('campus_program_id')->default(0);
            $table->integer('formb_min_amt')->default(0);
            $table->integer('charges')->default(0);
            $table->integer('exam_min_amt')->default(0);
            $table->integer('ca_min_amt')->default(0);
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
        Schema::dropIfExists('payment_items');
    }
}
