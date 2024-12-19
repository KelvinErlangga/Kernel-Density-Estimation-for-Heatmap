<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();

        $findUser = User::where('google_id', $user->id)->first();

        if ($findUser) {
            Auth::login($findUser);
        } else {

            $emailUser = User::where('email', $user->email)->first();

            if ($emailUser) {
                $emailUser->update([
                    'email' => $user->email,
                    'name' => $user->name,
                    'google_id' => $user->id
                ]);
                Auth::login($emailUser);
            } else {
                $newUser = User::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'google_id' => $user->id,
                    'password' => Hash::make('123123123')
                ]);

                $newUser->assignRole('pelamar');

                Auth::login($newUser);
            }
        }

        $loginUser = Auth::user();

        if ($loginUser->hasRole(['pelamar'])) {

            // Jika Role Pelamar akan masuk ke halaman awal
            return redirect()->intended(route('home'));
        } else if ($loginUser->hasRole(['perusahaan'])) {

            // Jika Role Perusahaan akan masuk ke halaman dashboard perusahaan
            return redirect()->intended(route('dashboard-perusahaan'));
        } else if ($loginUser->hasRole(['admin'])) {

            // Jika Role Admin akan masuk ke halaman dashboard admin
            return redirect()->intended(route('dashboard-admin'));
        } else {
            Auth::logout();
            return redirect()->route('login')->withErrors(['error' => 'Akses tidak diizinkan.']);
        }
    }
}
