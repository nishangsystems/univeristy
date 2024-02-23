<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('config_id');
            $table->enum('status', ['CURRENT', 'FORMER']);
            $table->integer('year_id')->nullable();
            $table->enum('delivery_format', ['HARD COPY', 'SOFT COPY']);
            $table->string('tel', 32)->nullable();
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
        Schema::dropIfExists('transcripts');
    }
}
