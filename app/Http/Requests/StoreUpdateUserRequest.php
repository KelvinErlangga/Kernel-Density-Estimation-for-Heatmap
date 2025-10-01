<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateUserRequest extends FormRequest
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
    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $passwordRule = $this->isMethod('post')
            ? ['required', 'string', 'min:8', 'confirmed']
            : ['nullable', 'string', 'min:8', 'confirmed'];

        return [
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'string', 'email', 'max:255', 'unique:users,email' . ($userId ? ",$userId" : '')],
            'password'               => $passwordRule,
            'role'                   => ['required', 'in:pelamar,perusahaan,admin'],

            // Pelamar
            'phone_pelamar'          => ['nullable', 'string', 'max:20'],
            'city_pelamar'           => ['nullable', 'string', 'max:255'],
            'gender'                 => ['nullable', 'in:laki-laki,perempuan'],
            'date_of_birth_pelamar'  => ['nullable', 'date'],

            // Perusahaan
            'phone_company'          => ['nullable', 'string', 'max:20'],
            'city_company'           => ['nullable', 'string', 'max:255'],
            'type_of_company'        => ['nullable', 'string', 'max:255'],
            'name_user_company'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nama',
            'email'    => 'email',
            'password' => 'password',
            'role'     => 'role',
        ];
    }
}
