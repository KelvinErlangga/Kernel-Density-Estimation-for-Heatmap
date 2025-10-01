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
}
