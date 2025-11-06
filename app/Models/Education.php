<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_vitae_user_id',
        'education_level',
        'school_name',
        'field_of_study',
        'city_education',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'gpa',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    public function curriculumVitaeUser()
    {
        return $this->belongsTo(CurriculumVitaeUser::class);
    }
}
