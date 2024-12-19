<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalCurriculumVitaesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_curriculum_vitaes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_vitae_user_id')->constrained()->onDelete('cascade');
            $table->string('avatar_curriculum_vitae')->nullable();
            $table->string('first_name_curriculum_vitae');
            $table->string('last_name_curriculum_vitae')->nullable();
            $table->string('email_curriculum_vitae');
            $table->string('phone_curriculum_vitae');
            $table->string('city_curriculum_vitae');
            $table->text('address_curriculum_vitae')->nullable();
            $table->text('personal_summary')->nullable();
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
        Schema::dropIfExists('personal_curriculum_vitaes');
    }
}
