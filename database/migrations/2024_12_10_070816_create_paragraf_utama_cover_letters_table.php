<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParagrafUtamaCoverLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paragraf_utama_cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_letter_user_id')->constrained()->onDelete('cascade');
            $table->text('paragraf_utama_1_cover_letter');
            $table->text('paragraf_utama_2_cover_letter')->nullable();
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
        Schema::dropIfExists('paragraf_utama_cover_letters');
    }
}
