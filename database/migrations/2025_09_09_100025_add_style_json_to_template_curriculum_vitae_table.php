<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStyleJsonToTemplateCurriculumVitaeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_curriculum_vitaes', function (Blueprint $table) {
            $table->json('style_json')->nullable()->after('layout_json');
        });
    }

    public function down()
    {
        Schema::table('template_curriculum_vitaes', function (Blueprint $table) {
            $table->dropColumn('style_json');
        });
    }
}
