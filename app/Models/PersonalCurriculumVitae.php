<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalCurriculumVitae extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_vitae_user_id',
        'avatar_curriculum_vitae',
        'first_name_curriculum_vitae',
        'last_name_curriculum_vitae',
        'email_curriculum_vitae',
        'phone_curriculum_vitae',
        'city_curriculum_vitae',
        'address_curriculum_vitae',
        'personal_summary'
    ];

    public function curriculumVitaeUser()
    {
        return $this->belongsTo(CurriculumVitaeUser::class);
    }
}
