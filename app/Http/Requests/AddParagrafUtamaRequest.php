<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddParagrafUtamaRequest extends FormRequest
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
            'paragraf_utama_1_cover_letter' => ['required', 'string', 'max:65535'],
            'paragraf_utama_2_cover_letter' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
