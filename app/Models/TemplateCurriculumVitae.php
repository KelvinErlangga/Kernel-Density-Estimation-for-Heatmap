<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateCurriculumVitae extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'template_curriculum_vitae_name',
        'thumbnail_curriculum_vitae',
        'layout_json',
        'style_json',
        'created_by',
        'template_type'
    ];

    protected $casts = [
        'layout_json' => 'array',
        'style_json'  => 'array',
    ];


    // get all template cv
    // public static function getAllTemplateCV()
    // {
    //     $templateCV = DB::table('template_curriculum_vitaes')->get();

    //     return $templateCV;
    // }

    public static function getAllTemplateCV()
    {
        return self::all(); // Eloquent otomatis exclude yang soft deleted
    }

    // get count template cv
    public static function getCountTemplateCV()
    {
        return self::count(); // otomatis exclude soft deleted
    }

    // relasi dengan tabel curriculum vitae user
    public function curriculumVitaeUser()
    {
        return $this->hasMany(CurriculumVitaeUser::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Ambil layout sebagai array
    public function getLayoutAttribute()
    {
        return $this->layout_json ? json_decode($this->layout_json, true) : [];
    }

    /** Parse layout_json → array PHP (aman jika null/invalid) */
    public function layoutArray(): array
    {
        $raw = $this->layout_json;

        // kalau sudah array karena casts → langsung pakai
        if (is_array($raw)) {
            return $raw;
        }

        // kalau string JSON → decode
        if (is_string($raw) && $raw !== '') {
            $arr = json_decode($raw, true);
            return is_array($arr) ? $arr : [];
        }

        return [];
    }

    /**
     * Ambil hanya “custom sections” yang dibuat admin.
     * Kriteria:
     * - key diawali 'custom' (mis. 'custom-ats' / 'custom-kreatif'), atau
     * - bukan termasuk whitelist section standar.
     * Support jika admin menaruh title/subtitle di item layout_json.
     */
    public function customSectionSpecs(): array
    {
        $items = $this->layoutArray();

        // daftar komponen standar yang BUKAN custom
        $whitelist = [
            'personal_detail',
            'summary',
            'objective',
            'experiences',
            'projects',
            'educations',
            'skills',
            'tools',
            'strengths',
            'languages',
            'certifications',
            'courses',
            'publications',
            'volunteering',
            'organizations',
            'achievements',
            'references',
            'interests',
            'links',
        ];

        $out = [];
        foreach ($items as $i => $it) {
            $key = $it['key'] ?? null;
            if (!$key) continue;

            // buang suffix -ats / -kreatif untuk cek whitelist
            $base = preg_replace('/-(ats|kreatif)$/i', '', $key);

            $isCustom = str_starts_with($base, 'custom') || !in_array($base, $whitelist, true);
            if (!$isCustom) continue;

            $out[] = [
                'key'      => $key,                                  // simpan apa adanya (dengan suffix)
                'title'    => $it['title']    ?? $it['section_title'] ?? null,
                'subtitle' => $it['subtitle'] ?? null,
            ];
        }
        return $out;
    }
}
