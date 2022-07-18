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
        Schema::create('video_conferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('created by');
            $table->string('recording_file_name')->unique();
            $table->string('from');
            $table->enum('recording_status', ['preparing', 'recording', 'recorded'] )->default('preparing');
            $table->string('recording_file_size')->nullable();
            $table->unsignedDecimal('price', 10, 2)->nullable();
            $table->unsignedInteger('number_of_downloads')->default(0);
            $table->timestamp('recording_at')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_conferences');
    }
};
