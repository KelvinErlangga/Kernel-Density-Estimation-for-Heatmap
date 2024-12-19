<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalCompanyCoverLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover_letter_user_id',
        'leader_company_cover_letter',
        'name_company_cover_letter',
        'email_company_cover_letter'
    ];

    public function coverLetterUser()
    {
        return $this->belongsTo(CoverLetterUser::class);
    }
}
