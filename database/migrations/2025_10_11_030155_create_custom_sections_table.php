<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_custom_sections_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('custom_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_vitae_user_id');
            $table->string('section_key');
            $table->string('section_title')->nullable();
            $table->string('subtitle')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('curriculum_vitae_user_id')
              ->references('id')->on('curriculum_vitae_users')
              ->onDelete('cascade');
            $table->index(['curriculum_vitae_user_id', 'section_key']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('custom_sections');
    }
};
