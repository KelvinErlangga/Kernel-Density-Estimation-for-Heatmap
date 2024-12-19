<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSocialMediaRequest extends FormRequest
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
            'link_name' => ['nullable', 'array'],
            'link_name.*' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'array'],
            'url.*' => ['nullable', 'string', 'max:255']
        ];
    }
}
