<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_custom_sections_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('custom_sections', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('curriculum_vitae_user_id');

            // key persis seperti di layout_json (biarkan ada suffix -ats/-kreatif kalau memang begitu)
            $t->string('section_key');

            // judul & subjudul yang diisi admin di layout_json (boleh null, bisa dioverride pelamar)
            $t->string('section_title')->nullable();
            $t->string('subtitle')->nullable();

            // isi dinamis pelamar — format bebas (array/richtext)
            $t->json('payload')->nullable();

            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();

            $t->foreign('curriculum_vitae_user_id')
              ->references('id')->on('curriculum_vitae_users')
              ->onDelete('cascade');

            $t->index(['curriculum_vitae_user_id', 'section_key']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('custom_sections');
    }
};
