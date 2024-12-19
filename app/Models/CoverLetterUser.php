<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CoverLetterUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_cover_letter_id'
    ];

    // get all cl
    public static function getCoverLetter()
    {
        $coverLetters = CoverLetterUser::with(['templateCL', 'user'])->get();

        return $coverLetters;
    }

    // relasi dengan tabel user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // relasi dengan tabel template cover letter
    public function templateCL()
    {
        return $this->belongsTo(TemplateCoverLetter::class, 'template_cover_letter_id');
    }

    public static function findByUserIdAndTemplateId($userId, $templateId)
    {
        return self::where('user_id', $userId)
            ->where('template_cover_letter_id', $templateId)
            ->first();
    }

    // relasi dengan tabel personal cover letter
    public function personalCoverLetter()
    {
        return $this->hasOne(PersonalCoverLetter::class);
    }

    // relasi dengan tabel personal company cover letter
    public function personalCompanyCoverLetter()
    {
        return $this->hasOne(PersonalCompanyCoverLetter::class);
    }

    // relasi dengan tabel paragraf pembuka cover letter
    public function paragrafPembukaCoverLetter()
    {
        return $this->hasOne(ParagrafPembukaCoverLetter::class);
    }

    // relasi dengan tabel paragraf utama cover letter
    public function paragrafUtamaCoverLetter()
    {
        return $this->hasOne(ParagrafUtamaCoverLetter::class);
    }

    // relasi dengan tabel paragraf penutup cover letter
    public function paragrafPenutupCoverLetter()
    {
        return $this->hasOne(ParagrafPenutupCoverLetter::class);
    }

    // relasi dengan tabel tanda tangan cover letter
    public function tandaTanganCoverLetter()
    {
        return $this->hasOne(TandaTanganCoverLetter::class);
    }
}
