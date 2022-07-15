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
        Schema::create('satisfactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_conference_id');
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->enum('ease', [1, 2, 3, 4, 5 ]);                                     // ความง่าย
            $table->enum('stability', [1, 2, 3, 4, 5 ]);                                // ความสเถียร
            $table->enum('sharpness', [1, 2, 3, 4, 5 ]);                                // ความคมชัด
            $table->text('comment')->nullable();                                        // ความคิดเห็น
            $table->timestamps();
            $table->foreign('video_conference_id')->references('id')->on('video_conferences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('satisfactions');
    }
};
