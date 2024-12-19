<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'hiring_id',
        'user_id',
        'file_applicant',
        'status'
    ];

    //relasi dengan tabel hiring
    public function hiring()
    {
        return $this->belongsTo(Hiring::class, 'hiring_id');
    }

    //relasi dengan tabel user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
