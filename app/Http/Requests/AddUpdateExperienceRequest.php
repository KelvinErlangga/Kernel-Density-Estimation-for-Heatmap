<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUpdateExperienceRequest extends FormRequest
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
            'position_experience' => ['required', 'string', 'max:255'],
            'company_experience' => ['required', 'string', 'max:255'],
            'city_experience' => ['nullable', 'string', 'max:255'],
            'description_experience' => ['nullable', 'string', 'max:65535'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
        ];
    }
}
