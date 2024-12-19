<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParagrafUtamaCoverLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover_letter_user_id',
        'paragraf_utama_1_cover_letter',
        'paragraf_utama_2_cover_letter',
    ];

    public function coverLetterUser()
    {
        return $this->belongsTo(CoverLetterUser::class);
    }
}
