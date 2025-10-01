<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSubmitAplicationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'hiring_id'      => ['required', 'integer'],
            'cv_option'      => ['required', 'in:upload,dashboard'],

            // kalau pilih upload, wajib file | kalau tidak, biarkan nullable
            'file_applicant' => ['nullable', 'required_if:cv_option,upload', 'file', 'mimes:pdf,jpeg,png,jpg'],

            // kalau pilih dashboard, wajib ada id CV
            'dashboard_cv'   => ['nullable', 'required_if:cv_option,dashboard', 'integer', 'exists:curriculum_vitae_users,id'],
        ];
    }

    public function messages()
    {
        return [
            'file_applicant.required_if' => 'File CV harus diunggah apabila memilih opsi Unggah dari File.',
            'dashboard_cv.required_if'   => 'Silakan pilih CV dari dashboard apabila memilih opsi Gunakan CV di Dashboard.',
        ];
    }
}
