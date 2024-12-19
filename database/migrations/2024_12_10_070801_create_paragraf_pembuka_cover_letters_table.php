<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParagrafPembukaCoverLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paragraf_pembuka_cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_letter_user_id')->constrained()->onDelete('cascade');
            $table->text('paragraf_pembuka_cover_letter');
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
        Schema::dropIfExists('paragraf_pembuka_cover_letters');
    }
}
