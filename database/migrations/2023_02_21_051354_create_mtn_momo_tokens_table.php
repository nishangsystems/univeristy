<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtnMomoTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mtn_momo_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->string('token_type')->default('Bearer');
            $table->enum('product', [
                'collection',
                'disbursement',
                'remittance',
            ]);
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mtn_momo_tokens');
    }
}
