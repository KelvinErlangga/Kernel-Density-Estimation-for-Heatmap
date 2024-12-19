<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalPelamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name_pelamar',
        'email_pelamar',
        'phone_pelamar',
        'city_pelamar',
        'gender',
        'date_of_birth_pelamar'
    ];
}
