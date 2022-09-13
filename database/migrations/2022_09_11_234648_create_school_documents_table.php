<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['GENERAL', 'SPECIFIC']);
            $table->unsignedBigInteger('degree_id')->nullable(true);
            $table->unsignedBigInteger('program_id')->nullable(true);
            $table->unsignedBigInteger('school_level_id')->nullable(true);
            $table->string('document_path');
            $table->timestamps();

            $table->foreign('degree_id')->references('id')->on('degrees');
            $table->foreign('program_id')->references('id')->on('programs');
            $table->foreign('school_level_id')->references('id')->on('school_levels');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_documents');
    }
}
