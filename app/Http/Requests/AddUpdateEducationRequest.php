<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddUpdateEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Jika ingin membatasi pilihan jenjang sesuai opsi di form,
     * aktifkan Rule::in($this->educationLevels()) pada rules().
     */
    private function educationLevels(): array
    {
        return [
            'SMA/MA/SMK',
            'D1',
            'D2',
            'D3',
            'D4',
            'S1',
            'S2',
            'S3',
        ];
    }

    public function rules(): array
    {
        return [
            // WAJIB sesuai form
            'education_level' => [
                'required',
                'string',
                // => jika ingin strict ke opsi form, buka komentar:
                // Rule::in($this->educationLevels()),
            ],
            'school_name'     => ['required', 'string', 'max:255'],
            'city_education'  => ['required', 'string', 'max:255'],

            // OPSIONAL
            'field_of_study'  => ['nullable', 'string', 'max:255'],
            'gpa'             => ['nullable', 'string', 'max:50'],

            // TANGGAL dari <input type="month"> => "YYYY-MM"
            'start_date'      => ['required', 'date_format:Y-m'],
            'end_date'        => ['nullable', 'date_format:Y-m', 'prohibited_if:is_current,1'],

            // Checkbox "sedang berjalan"
            'is_current'      => ['nullable', 'boolean'],

            // Deskripsi rich text dari Quill (HTML)
            'description'     => ['nullable', 'string'],
        ];
    }

    /**
     * Rapikan input sebelum validasi:
     * - pastikan is_current jadi boolean
     * - jika is_current = 1, kosongkan end_date agar lolos rule & konsisten
     * - trim beberapa field teks
     */
    protected function prepareForValidation(): void
    {
        $isCurrent = filter_var($this->input('is_current'), FILTER_VALIDATE_BOOLEAN);

        $this->merge([
            'is_current'     => $isCurrent,
            'end_date'       => $isCurrent ? null : $this->input('end_date'),
            'education_level' => $this->input('education_level') !== null ? trim($this->input('education_level')) : null,
            'school_name'    => $this->input('school_name')     !== null ? trim($this->input('school_name'))     : null,
            'city_education' => $this->input('city_education')  !== null ? trim($this->input('city_education'))  : null,
            'field_of_study' => $this->input('field_of_study')  !== null ? trim($this->input('field_of_study'))  : null,
            'gpa'            => $this->input('gpa')             !== null ? trim($this->input('gpa'))             : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'education_level.required' => 'Jenjang pendidikan wajib diisi.',
            'education_level.in'       => 'Jenjang pendidikan tidak sesuai opsi yang tersedia.',
            'school_name.required'     => 'Nama sekolah/kampus wajib diisi.',
            'city_education.required'  => 'Kota pendidikan wajib diisi.',

            'start_date.required'      => 'Tanggal mulai wajib diisi.',
            'start_date.date_format'   => 'Format mulai harus YYYY-MM.',
            'end_date.date_format'     => 'Format selesai harus YYYY-MM.',
            'end_date.prohibited_if'   => 'Tanggal selesai harus dikosongkan jika status masih berjalan.',

            'is_current.boolean'       => 'Status sedang berjalan tidak valid.',
            'gpa.max'                  => 'Panjang nilai/IPK terlalu panjang.',
        ];
    }

    public function attributes(): array
    {
        return [
            'education_level' => 'jenjang pendidikan',
            'school_name'     => 'nama sekolah/kampus',
            'city_education'  => 'kota pendidikan',
            'field_of_study'  => 'jurusan/program studi',
            'start_date'      => 'mulai',
            'end_date'        => 'selesai',
            'is_current'      => 'status masih berjalan',
            'gpa'             => 'IPK/nilai',
            'description'     => 'deskripsi',
        ];
    }
}
