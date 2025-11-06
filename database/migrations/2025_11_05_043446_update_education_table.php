<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safety: pastikan nilai NULL pada city_education diisi dulu agar bisa dibuat NOT NULL
        DB::table('education')->whereNull('city_education')->update(['city_education' => '-']);

        Schema::table('education', function (Blueprint $table) {
            // 1) Tambah kolom baru
            // Jenjang pendidikan (enum sesuai opsi pada form)
            $table->enum('education_level', [
                'SD/MI',
                'SMP/MTs',
                'SMA/MA/SMK',
                'D1',
                'D2',
                'D3',
                'D4',
                'S1',
                'S2',
                'S3',
            ])->after('curriculum_vitae_user_id');

            // Status masih berjalan
            $table->boolean('is_current')->default(false)->after('end_date');

            // Nilai/IPK: fleksibel (bisa "3.75" atau "90/100"), jadi simpan string
            $table->string('gpa', 50)->nullable()->after('is_current');

            // 2) Ubah struktur existing fields
            // city_education sekarang WAJIB (NOT NULL)
            $table->string('city_education')->nullable(false)->change();

            // field_of_study dijadikan nullable (opsional untuk SD/SMP)
            $table->string('field_of_study')->nullable()->change();

            // description_education -> description (longText untuk HTML dari Quill)
            // rename kolom lalu perbesar tipe data
        });

        // Rename kolom deskripsi di langkah terpisah (beberapa driver lebih stabil dipisah)
        if (Schema::hasColumn('education', 'description_education')) {
            Schema::table('education', function (Blueprint $table) {
                $table->renameColumn('description_education', 'description');
            });
        }

        // Ubah tipe deskripsi menjadi longText agar aman menampung HTML Quill
        Schema::table('education', function (Blueprint $table) {
            $table->longText('description')->nullable()->change();
        });

        // 3) Pastikan end_date tetap nullable (sesuai form saat "sedang berjalan")
        Schema::table('education', function (Blueprint $table) {
            $table->date('end_date')->nullable()->change();

            // 4) (Opsional) Index untuk sorting/filter
            $table->index(['start_date']);
            $table->index(['end_date']);
            $table->index(['education_level']);
        });

        // (Opsional) Constraint logis (MySQL 8+) — end_date harus NULL jika is_current = 1
        // Komentari jika DB Anda tidak mendukung CHECK.
        try {
            DB::statement("
                ALTER TABLE education
                ADD CONSTRAINT chk_education_current
                CHECK (
                    (is_current = 1 AND end_date IS NULL)
                    OR (is_current = 0)
                )
            ");
        } catch (\Throwable $e) {
            // Abaikan jika engine tidak mendukung CHECK
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus CHECK constraint jika ada
        try {
            DB::statement("ALTER TABLE education DROP CONSTRAINT chk_education_current");
        } catch (\Throwable $e) {
            // Abaikan jika tidak ada / tidak didukung
        }

        // Kembalikan perubahan tipe kolom deskripsi
        if (Schema::hasColumn('education', 'description')) {
            Schema::table('education', function (Blueprint $table) {
                $table->text('description')->nullable()->change();
            });
            // rename kembali ke nama lama
            Schema::table('education', function (Blueprint $table) {
                $table->renameColumn('description', 'description_education');
            });
        }

        Schema::table('education', function (Blueprint $table) {
            // Kembalikan city_education menjadi nullable
            $table->string('city_education')->nullable()->change();

            // Kembalikan field_of_study menjadi NOT NULL (sesuai skema awal)
            $table->string('field_of_study')->nullable(false)->change();

            // Hapus kolom baru
            if (Schema::hasColumn('education', 'education_level')) {
                $table->dropColumn('education_level');
            }
            if (Schema::hasColumn('education', 'is_current')) {
                $table->dropColumn('is_current');
            }
            if (Schema::hasColumn('education', 'gpa')) {
                $table->dropColumn('gpa');
            }

            // Hapus index opsional
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['education_level']);
        });
    }
};
