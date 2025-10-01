<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToHiringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hirings', function (Blueprint $table) {
            $table->integer('jumlah_karyawan')->nullable();
            $table->string('ukuran_perusahaan')->nullable();
            $table->string('sektor_industri')->nullable();

            $table->text('kualifikasi')->nullable();
            $table->integer('pengalaman_minimal_tahun')->nullable();
            $table->integer('usia_maksimal')->nullable();
            $table->text('keterampilan_teknis')->nullable();
            $table->text('keterampilan_non_teknis')->nullable();

            $table->string('jenis_pekerjaan')->nullable();
            $table->unsignedBigInteger('gaji_min')->nullable();
            $table->unsignedBigInteger('gaji_max')->nullable();

            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hirings', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_karyawan',
                'ukuran_perusahaan',
                'sektor_industri',
                'kualifikasi',
                'pengalaman_minimal_tahun',
                'usia_maksimal',
                'keterampilan_teknis',
                'keterampilan_non_teknis',
                'jenis_pekerjaan',
                'gaji_min',
                'gaji_max',
                'kota',
                'provinsi',
                'latitude',
                'longitude',
            ]);
        });
    }
}
