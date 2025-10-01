<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLayoutJsonAndCreatedByToTemplateCurriculumVitaesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_curriculum_vitaes', function (Blueprint $table) {
            $table->longText('layout_json')->nullable()->after('thumbnail_curriculum_vitae');
            $table->foreignId('created_by')->nullable()->after('layout_json')->constrained('users');
        });
    }

    public function down()
    {
        Schema::table('template_curriculum_vitaes', function (Blueprint $table) {
            $table->dropColumn('layout_json');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
}
