<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_name',
        'category_skill'
    ];

    // get all skill
    public static function getSkill()
    {
        $skills = DB::table('skills')->get();

        return $skills;
    }

    // get count skill
    public static function getCountSkill()
    {
        $countskills = DB::table('skills')->count();

        return $countskills;
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
}
