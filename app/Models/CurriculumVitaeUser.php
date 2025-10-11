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
        'template_curriculum_vitae_id',
        'pdf_file',
        'pdf_filename',
    ];

    protected $guarded = [];

    // get all cv
    public static function getCurriculumVitaeUser($userId)
    {
        $curriculumVitaes = CurriculumVitaeUser::with(['templateCV', 'user'])->where('user_id', $userId)
            ->get();

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
    public function personalDetail()
    {
        return $this->hasOne(PersonalDetail::class);
    }

    // relasi dengan tabel education
    public function educations()
    {
        return $this->hasMany(Education::class, 'curriculum_vitae_user_id', 'id');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'curriculum_vitae_user_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(SkillUser::class, 'curriculum_vitae_user_id', 'id');
    }

    public function languages()
    {
        return $this->hasMany(Language::class, 'curriculum_vitae_user_id', 'id');
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

    public function customs()
    {
        return $this->hasMany(CustomSection::class)
            ->orderBy('sort_order');
    }

    public function template()
    {
        return $this->belongsTo(TemplateCurriculumVitae::class, 'template_curriculum_vitae_id');
    }

    public function customSections()
    {
        return $this->hasMany(\App\Models\CustomSection::class)
            ->orderBy('sort_order');
    }
}
