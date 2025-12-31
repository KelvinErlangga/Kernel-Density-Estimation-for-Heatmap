<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->string('category_level', 100)->nullable()->change();
        });

        // Konversi data lama yang masih "A1..C2" menjadi "CEFR|A1..C2"
        DB::table('languages')
            ->whereIn('category_level', ['A1','A2','B1','B2','C1','C2'])
            ->update(values: [
                'category_level' => DB::raw("CONCAT('CEFR|', category_level)")
            ]);
    }

    public function down(): void
    {
        // Balikkan hanya untuk data yang berawalan CEFR|
        DB::table('languages')
            ->where('category_level', 'like', 'CEFR|%')
            ->update([
                'category_level' => DB::raw("REPLACE(category_level, 'CEFR|', '')")
            ]);

        Schema::table('languages', function (Blueprint $table) {
            $table->string('category_level', 10)->nullable()->change();
        });
    }
};
