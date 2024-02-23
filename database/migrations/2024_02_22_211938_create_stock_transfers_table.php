<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->integer('sender_campus')->nullable();
            $table->integer('receiver_campus')->nullable();
            $table->integer('user_id');
            $table->integer('stock_id');
            $table->enum('type', ['send', 'restore', 'receive']);
            $table->integer('quantity')->nullable();
            $table->boolean('restored')->default(0);
            $table->integer('year_id');
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
        Schema::dropIfExists('stock_transfers');
    }
}
