<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PendingPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('pending_promotions');

        Schema::create('pending_promotions', function(Blueprint $table){
            $table->engine="InnoDB";
            $table->id();
            $table->unsignedBigInteger('from_year');
            $table->unsignedBigInteger('to_year');
            $table->unsignedBigInteger('from_class');
            $table->unsignedBigInteger('to_class');
            $table->enum('type', ['promotion', 'demotion']);
            $table->string('students');
            $table->timestamp('created_at')->default(now());
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('pending_promotions');
    }
}
