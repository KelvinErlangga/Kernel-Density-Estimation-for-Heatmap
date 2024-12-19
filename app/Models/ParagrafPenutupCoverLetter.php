<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParagrafPenutupCoverLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover_letter_user_id',
        'paragraf_penutup_cover_letter',
    ];

    public function coverLetterUser()
    {
        return $this->belongsTo(CoverLetterUser::class);
    }
}
