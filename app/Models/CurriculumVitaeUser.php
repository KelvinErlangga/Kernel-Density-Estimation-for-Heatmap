<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurriculumVitaeUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_curriculum_vitae_id'
    ];

    // get all cv
    public static function getCurriculumVitaeUser($userId)
    {
        $curriculumVitaes = CurriculumVitaeUser::with(['templateCV', 'user'])->where('user_id', $userId)
            ->get();;

        return $curriculumVitaes;
    }

    // relasi dengan tabel user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // relasi dengan tabel template curriculum vitae
    public function templateCV()
    {
        return $this->belongsTo(TemplateCurriculumVitae::class, 'template_curriculum_vitae_id');
    }

    public static function findByUserIdAndTemplateId($userId, $templateId)
    {
        return self::where('user_id', $userId)
            ->where('template_curriculum_vitae_id', $templateId)
            ->first();
    }

    // relasi dengan tabel personal curriculum vitae
    public function personalCurriculumVitae()
    {
        return $this->hasOne(PersonalCurriculumVitae::class);
    }

    // relasi dengan tabel education
    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    // relasi dengan tabel experience
    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    // relasi dengan tabel language
    public function languages()
    {
        return $this->hasMany(Language::class);
    }

    // relasi dengan tabel skill
    public function skills()
    {
        return $this->hasMany(SkillUser::class);
    }

    // relasi dengan tabel organisasi
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    // relasi dengan tabel achievement
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    // relasi dengan tabel social media
    public function links()
    {
        return $this->hasMany(SocialMedia::class);
    }
}
