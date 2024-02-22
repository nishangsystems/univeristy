<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('year_id');
            $table->integer('item_id');
            $table->integer('semester_id')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->integer('amount');
            $table->boolean('parent')->default(0);
            $table->boolean('used')->default(1);
            $table->string('financialTransactionId', 64);
            $table->enum('type', ['PLATFORM', 'RESULTS', 'TRANSCRIPT']);
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
        Schema::dropIfExists('charges');
    }
}
