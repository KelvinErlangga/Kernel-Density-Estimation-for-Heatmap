<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:13'],
            'gender' => ['string'],
            'date_of_birth_pelamar' => ['date'],
            'name_user_company' => ['string', 'max:255'],
            'type_of_company' => ['string', 'max:255'],
        ]);

        // Jika user memilih role pelamar
        if ($request->gender) {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('pelamar');

            // data personal pelamar
            $user->personalPelamar()->create([
                'user_id' => $user->id,
                'name_pelamar' => $request->name,
                'email_pelamar' => $request->email,
                'phone_pelamar' => $request->phone,
                'city_pelamar' => $request->city,
                'gender' => $request->gender,
                'date_of_birth_pelamar' => $request->date_of_birth_pelamar
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('home'));
        } elseif ($request->type_of_company) {
            // Jika user memilih role perusahaan
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('perusahaan');

            // data personal perusahaan
            $user->personalComapany()->create([
                'user_id' => $user->id,
                'name_company' => $request->name,
                'email_company' => $request->email,
                'phone_company' => $request->phone,
                'city_company' => $request->city,
                'type_of_company' => $request->type_of_company,
                'name_user_company' => $request->name_user_company
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard-perusahaan'));
        }
    }
}
