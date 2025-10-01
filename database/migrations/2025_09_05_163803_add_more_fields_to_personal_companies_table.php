<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToPersonalCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('personal_companies', function (Blueprint $table) {
            $table->integer('jumlah_karyawan')->nullable()->after('description_company');
            $table->integer('jumlah_divisi')->nullable()->after('jumlah_karyawan');
            $table->year('tahun_berdiri')->nullable()->after('jumlah_divisi');
            $table->string('logo')->nullable()->after('tahun_berdiri');
        });
    }

    public function down(): void
    {
        Schema::table('personal_companies', function (Blueprint $table) {
            $table->dropColumn(['jumlah_karyawan', 'jumlah_divisi', 'tahun_berdiri', 'logo']);
        });
    }
}
