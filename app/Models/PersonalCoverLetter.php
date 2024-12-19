<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalCoverLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover_letter_user_id',
        'name_cover_letter',
        'position_cover_letter',
        'phone_cover_letter',
        'email_cover_letter',
        'portofolio_cover_letter',
        'linkedin_cover_letter'
    ];

    public function coverLetterUser()
    {
        return $this->belongsTo(CoverLetterUser::class);
    }
}
