<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUpdateProfileCurriculumVitaeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar_curriculum_vitae' => ['nullable', 'image', 'mimes:jpeg,jpg,png'],
            'first_name_curriculum_vitae' => ['required', 'string', 'max:255'],
            'last_name_curriculum_vitae' => ['nullable', 'string', 'max:255'],
            'email_curriculum_vitae' => ['required', 'string', 'max:255'],
            'phone_curriculum_vitae' => ['required', 'string', 'max:13'],
            'city_curriculum_vitae' => ['required', 'string', 'max:255'],
            'address_curriculum_vitae' => ['nullable', 'string', 'max:65535'],
            'personal_summary' => ['nullable', 'string', 'max:65535']
        ];
    }
}
