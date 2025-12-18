<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddLanguageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $levels = ['A1','A2','B1','B2','C1','C2'];

        return [
            'language_name'   => ['nullable', 'array'],
            'language_name.*' => ['nullable', 'string', 'max:255'],

            'level'   => ['nullable', 'array'],
            'level.*' => ['nullable', Rule::in($levels)],
        ];
    }

    public function messages()
    {
        return [
            'level.*.in' => 'Level bahasa harus salah satu dari: A1, A2, B1, B2, C1, C2.',
        ];
    }
}
