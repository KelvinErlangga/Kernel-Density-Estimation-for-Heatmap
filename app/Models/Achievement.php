<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_vitae_user_id',
        'achievement_name',
        'organizer_achievement',
        'city_achievement',
        'description_achievement',
        'date_achievement'
    ];
}
