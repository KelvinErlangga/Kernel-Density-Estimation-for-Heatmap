<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecommendedSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'skill_id'
    ];

    // get all recommended skill
    public static function getRecommendedSkill()
    {
        $recommended_skills = RecommendedSkill::with(['job', 'skill'])->get();

        return $recommended_skills;
    }

    // get count recommended skill
    public static function getCountRecommendedSkill()
    {
        $recommended_skills = DB::table('recommended_skills')->count();

        return $recommended_skills;
    }

    // relasi dengan tabel job
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // relasi dengan tabel skill
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
