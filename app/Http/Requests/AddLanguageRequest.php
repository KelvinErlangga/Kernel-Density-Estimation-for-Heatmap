<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddLanguageRequest extends FormRequest
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
            'language_name' => ['nullable', 'array'],
            'language_name.*' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'array'],
            'level.*' => ['nullable', 'string', 'max:255']
        ];
    }
}
