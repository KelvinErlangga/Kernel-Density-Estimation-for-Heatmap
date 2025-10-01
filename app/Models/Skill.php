<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'skill_name',
        'category_skill',
        'curriculum_vitae_user_id',
    ];

    // get all skill
    public static function getSkill()
    {
        return DB::table('skills')->get();
    }

    // get count skill
    public static function getCountSkill()
    {
        return DB::table('skills')->count();
    }

    public function recommendedSkill()
    {
        return $this->hasMany(RecommendedSkill::class);
    }

    // relasi dengan tabel skill user
    public function skillUser()
    {
        return $this->hasMany(SkillUser::class);
    }

    public function cvUser()
    {
        return $this->belongsTo(CurriculumVitaeUser::class, 'curriculum_vitae_user_id');
    }

    // Normalisasi skill name
    public function setSkillNameAttribute($value)
    {
        $this->attributes['skill_name'] = strtolower(trim($value));
    }
}
