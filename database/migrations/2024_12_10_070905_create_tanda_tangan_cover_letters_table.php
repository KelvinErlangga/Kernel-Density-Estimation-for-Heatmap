<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTandaTanganCoverLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tanda_tangan_cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_letter_user_id')->constrained()->onDelete('cascade');
            $table->string('name_tanda_tangan_cover_letter');
            $table->date('date_tanda_tangan_cover_letter');
            $table->string('tanda_tangan_cover_letter')->nullable();
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
        Schema::dropIfExists('tanda_tangan_cover_letters');
    }
}
