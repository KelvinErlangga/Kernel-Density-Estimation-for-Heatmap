<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hiring extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'personal_company_id',
        'position_hiring',
        'address_hiring',
        'work_system',
        'pola_kerja',
        'education_hiring',
        'deadline_hiring',
        'description_hiring',
        'latitude',
        'longitude',
        'ukuran_perusahaan',
        'sektor_industri',
        'kualifikasi',
        'pengalaman_minimal_tahun',
        'usia_maksimal',
        'keterampilan_teknis',
        'keterampilan_non_teknis',
        'jenis_pekerjaan',
        'gaji_min',
        'gaji_max',
        'kota',
        'provinsi',
    ];

    // app/Models/Hiring.php
    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // relasi dengan table personal company
    public function personalCompany()
    {
        return $this->belongsTo(PersonalCompany::class, 'personal_company_id');
    }

    // relasi dengan tabel applicant
    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
