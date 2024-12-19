<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hiring extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_company_id',
        'position_hiring',
        'address_hiring',
        'type_of_work',
        'work_system',
        'pola_kerja',
        'education_hiring',
        'gaji',
        'deadline_hiring',
        'description_hiring'
    ];

    // relasi dengan table personal company
    public function personalCompany()
    {
        return $this->belongsTo(PersonalCompany::class, 'personal_company_id');
    }

    //relasi dengan tabel applicant
    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
