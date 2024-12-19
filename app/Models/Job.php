<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_name'
    ];

    // get all job
    public static function getJob()
    {
        $jobs = DB::table('jobs')->get();

        return $jobs;
    }

    // get count job
    public static function getCountJob()
    {
        $countjobs = DB::table('jobs')->count();

        return $countjobs;
    }

    public function recommendedSkill()
    {
        return $this->hasMany(RecommendedSkill::class);
    }
}
