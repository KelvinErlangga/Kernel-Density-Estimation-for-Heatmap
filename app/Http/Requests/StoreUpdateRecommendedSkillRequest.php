<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRecommendedSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
        ];

        // STORE (POST): skill_id harus array (multi insert)
        if ($this->isMethod('post')) {
            $rules['skill_id'] = ['required', 'array', 'min:1'];
            $rules['skill_id.*'] = ['required', 'integer', 'exists:skills,id'];
        }
        // UPDATE (PUT/PATCH): skill_id single value
        else {
            $rules['skill_id'] = ['required', 'integer', 'exists:skills,id'];
        }

        return $rules;
    }

    /**
     * Custom messages (optional tapi membantu debugging).
     */
    public function messages(): array
    {
        return [
            'job_id.required' => 'Pekerjaan wajib dipilih.',
            'job_id.exists' => 'Pekerjaan tidak valid.',
            'skill_id.required' => 'Keahlian wajib dipilih.',
            'skill_id.array' => 'Format skill_id harus berupa array (khusus tambah data).',
            'skill_id.min' => 'Minimal pilih 1 keahlian.',
            'skill_id.*.exists' => 'Keahlian yang dipilih tidak valid.',
        ];
    }
}
