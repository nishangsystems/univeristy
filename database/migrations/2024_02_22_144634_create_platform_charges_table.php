<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_charges', function (Blueprint $table) {
            $table->id();
            $table->integer('year_id');
            $table->integer('yearly_amount')->nullable();
            $table->integer('parent_amount')->default(0);
            $table->integer('transcript_amount')->nullable();
            $table->integer('result_amount')->nullable();
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
        Schema::dropIfExists('platform_charges');
    }
}
