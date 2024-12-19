<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHiringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hirings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_company_id')->constrained()->onDelete('cascade');
            $table->string('position_hiring');
            $table->text('address_hiring');
            $table->string('type_of_work');
            $table->string('work_system');
            $table->string('pola_kerja');
            $table->string('education_hiring');
            $table->unsignedBigInteger('gaji');
            $table->date('deadline_hiring');
            $table->text('description_hiring');
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
        Schema::dropIfExists('hirings');
    }
}
