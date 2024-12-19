<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_vitae_user_id',
        'position_organization',
        'organization_name',
        'city_organization',
        'description_organization',
        'start_date',
        'end_date'
    ];
}
