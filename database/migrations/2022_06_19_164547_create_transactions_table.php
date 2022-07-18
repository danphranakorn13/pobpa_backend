<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_conference_id');
            $table->unsignedBigInteger('user_id')->comment('created by');                                                                                                
            $table->unsignedDecimal('price', 10, 2);
            $table->enum('status', ['G', 'A', 'S', 'V', 'D'] )->comment('G = Generate , A = Authorize , S = Settle, V = Void, D = Decline'); 
            $table->enum('payment_method', ['Q', 'C'] )->comment('Q = Qr Cash, C = Credit Card Full payment');
            $table->text('response')->nullable()->comment('Response from GB pay');;
            $table->foreign('video_conference_id')->references('id')->on('video_conferences');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('transactions');
    }
};
