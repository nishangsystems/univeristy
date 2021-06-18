<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->string('name');                
            $table->double('amount_spend', 8, 2);        
            $table->longText('description');
            $table->string('balance');
            $table->date('date');	
=======
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->decimal('amount_spend', 8, 2);
            $table->decimal('balance', 8, 2);
            $table->longText('description');
            $table->date('date');
>>>>>>> 13b80ef21fd8127feeb7647e60df639f45e24383
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
        Schema::dropIfExists('expenses');
    }
}
