<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTanganCoverLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover_letter_user_id',
        'name_tanda_tangan_cover_letter',
        'date_tanda_tangan_cover_letter',
        'tanda_tangan_cover_letter',
    ];

    public function coverLetterUser()
    {
        return $this->belongsTo(CoverLetterUser::class);
    }
}
