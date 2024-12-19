<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddParagrafPenutupRequest extends FormRequest
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
            'paragraf_penutup_cover_letter' => ['required', 'string', 'max:65535'],
        ];
    }
}
