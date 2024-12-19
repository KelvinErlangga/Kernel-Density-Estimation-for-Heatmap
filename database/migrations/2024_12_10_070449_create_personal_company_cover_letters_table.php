<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalCompanyCoverLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_company_cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_letter_user_id')->constrained()->onDelete('cascade');
            $table->string('leader_company_cover_letter');
            $table->string('name_company_cover_letter');
            $table->string('email_company_cover_letter');
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
        Schema::dropIfExists('personal_company_cover_letters');
    }
}
