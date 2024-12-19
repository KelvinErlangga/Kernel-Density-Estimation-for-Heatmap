<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_vitae_user_id',
        'position_experience',
        'company_experience',
        'city_experience',
        'description_experience',
        'start_date',
        'end_date'
    ];

    // relasi curriculum vitae user
    public function curriculumVitaeUser()
    {
        return $this->belongsTo(CurriculumVitaeUser::class);
    }
}
