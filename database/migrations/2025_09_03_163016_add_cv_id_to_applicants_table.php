<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCvIdToApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->unsignedBigInteger('curriculum_vitae_user_id')->nullable()->after('user_id');

            $table->foreign('curriculum_vitae_user_id')
                ->references('id')->on('curriculum_vitae_users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['curriculum_vitae_user_id']);
            $table->dropColumn('curriculum_vitae_user_id');
        });
    }
}
