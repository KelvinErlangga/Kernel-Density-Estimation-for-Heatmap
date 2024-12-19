<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TemplateCoverLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_cover_letter_name',
        'thumbnail_cover_letter'
    ];

    // get all template cover letter
    public static function getAllTemplateCoverLetter()
    {
        $templateCoverLetter = DB::table('template_cover_letters')->get();

        return $templateCoverLetter;
    }

    // get count template cover letter
    public static function getCountTemplateCoverLetter()
    {
        $countTemplateCoverLetter = DB::table('template_cover_letters')->count();

        return $countTemplateCoverLetter;
    }

    // relasi dengan tabel cover letter user
    public function coverLetterUser()
    {
        return $this->hasMany(CoverLetterUser::class);
    }
}
