<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalCoverLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_letter_user_id')->constrained()->onDelete('cascade');
            $table->string('name_cover_letter');
            $table->string('position_cover_letter');
            $table->string('phone_cover_letter');
            $table->string('email_cover_letter');
            $table->string('portofolio_cover_letter')->nullable();
            $table->string('linkedin_cover_letter')->nullable();
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
        Schema::dropIfExists('personal_cover_letters');
    }
}
