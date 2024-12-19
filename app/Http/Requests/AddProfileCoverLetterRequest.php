<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProfileCoverLetterRequest extends FormRequest
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
            'name_cover_letter' => ['required', 'string', 'max:255'],
            'position_cover_letter' => ['required', 'string', 'max:255'],
            'phone_cover_letter' => ['required', 'string', 'max:255'],
            'email_cover_letter' => ['required', 'string', 'max:255'],
            'portofolio_cover_letter' => ['nullable', 'string', 'max:255'],
            'linkedin_cover_letter' => ['nullable', 'string', 'max:255'],
        ];
    }
}
