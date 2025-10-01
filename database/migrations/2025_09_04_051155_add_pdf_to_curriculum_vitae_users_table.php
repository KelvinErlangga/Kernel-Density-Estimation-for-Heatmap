<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfToCurriculumVitaeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('curriculum_vitae_users', function (Blueprint $table) {
            $table->longText('pdf_file')->nullable(); // simpan base64 dari file PDF
            $table->string('pdf_filename')->nullable();
        });
    }

    public function down()
    {
        Schema::table('curriculum_vitae_users', function (Blueprint $table) {
            $table->dropColumn(['pdf_file', 'pdf_filename']);
        });
    }
}
