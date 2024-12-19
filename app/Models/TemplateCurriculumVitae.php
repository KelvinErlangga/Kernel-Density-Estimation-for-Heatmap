<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TemplateCurriculumVitae extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_curriculum_vitae_name',
        'thumbnail_curriculum_vitae'
    ];

    // get all template cv
    public static function getAllTemplateCV()
    {
        $templateCV = DB::table('template_curriculum_vitaes')->get();

        return $templateCV;
    }

    // get count template cv
    public static function getCountTemplateCV()
    {
        $countTemplateCV = DB::table('template_curriculum_vitaes')->count();

        return $countTemplateCV;
    }

    // relasi dengan tabel curriculum vitae user
    public function curriculumVitaeUser()
    {
        return $this->hasMany(CurriculumVitaeUser::class);
    }
}
