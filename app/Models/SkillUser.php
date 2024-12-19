<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_vitae_user_id',
        'skill_name',
        'category_level'
    ];
}
