<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUpdateAchievementRequest extends FormRequest
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
            'achievement_name' => ['required', 'string', 'max:255'],
            'organizer_achievement' => ['required', 'string', 'max:255'],
            'city_achievement' => ['nullable', 'string', 'max:255'],
            'description_achievement' => ['nullable', 'string', 'max:65535'],
            'date_achievement' => ['required', 'date']
        ];
    }
}
