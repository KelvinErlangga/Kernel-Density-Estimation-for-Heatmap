<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateTypeToTemplateCurriculumVitaesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_curriculum_vitaes', function (Blueprint $table) {
            // Bisa pakai enum atau string, tergantung kebutuhan
            $table->enum('template_type', ['ATS', 'Kreatif'])
                ->default('ATS')
                ->after('style_json');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_curriculum_vitaes', function (Blueprint $table) {
            $table->dropColumn('template_type');
        });
    }
}
