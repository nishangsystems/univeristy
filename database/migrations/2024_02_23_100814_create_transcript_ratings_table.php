<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranscriptRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcript_ratings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['SUPER FAST MODE', 'FAST MODE', 'NORMAL MODE']);
            $table->integer('duration');
            $table->integer('current_price');
            $table->integer('former_price');
            $table->integer('user_id');
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
        Schema::dropIfExists('transcript_ratings');
    }
}
