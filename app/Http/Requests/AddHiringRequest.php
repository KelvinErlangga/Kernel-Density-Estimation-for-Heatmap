<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddHiringRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'position_hiring'          => ['required', 'string', 'max:255'],
            'address_hiring'           => ['required', 'string'],
            'work_system'              => ['required', 'string', 'max:255'],
            'pola_kerja'               => ['required', 'string', 'max:255'],
            'jenis_pekerjaan'          => ['required', 'string', 'max:255'],
            'gaji_min'                 => ['required', 'numeric', 'min:0'],
            'gaji_max'                 => ['required', 'numeric', 'min:0'],
            'ukuran_perusahaan'        => ['required', 'string', 'max:255'],
            'sektor_industri'          => ['required', 'string', 'max:255'],
            'kualifikasi'              => ['required', 'string'],
            'pengalaman_minimal_tahun' => ['required', 'integer', 'min:0'],
            'usia_maksimal'            => ['required', 'integer', 'min:0'],
            'keterampilan_teknis'      => ['required', 'string'],
            'keterampilan_non_teknis'  => ['required', 'string'],
            'kota'                     => ['required', 'string', 'max:255'],
            'provinsi'                 => ['required', 'string', 'max:255'],
            'deadline_hiring'          => ['required', 'date'],
            'latitude'                 => ['required', 'numeric'],
            'longitude'                => ['required', 'numeric'],
            'education_hiring'         => ['required', 'string', 'max:255'],
            'description_hiring'       => ['required', 'string'],
        ];
    }
}
