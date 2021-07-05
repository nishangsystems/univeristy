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
            $table->decimal('amount_payable', 8, 2);
            $table->decimal('total_amount', 8, 2);
            $table->decimal('balance', 8, 2);
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
