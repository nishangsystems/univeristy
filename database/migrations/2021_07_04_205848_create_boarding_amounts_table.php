<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardingAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boarding_amounts', function (Blueprint $table) {
            $table->id();
            $table->integer('amount_payable');
            $table->integer('total_amount');
            $table->integer('balance');
            $table->tinyInteger('status');
            $table->integer('collect_boarding_fee_id')->unsigned();
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
        Schema::dropIfExists('boarding_amounts');
    }
}
