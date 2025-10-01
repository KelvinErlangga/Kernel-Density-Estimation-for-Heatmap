<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PersonalPelamar;
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
        $googleUser = Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->stateless()
            ->user();

        // Validasi email dari Google
        if (empty($googleUser->email) || !filter_var($googleUser->email, FILTER_VALIDATE_EMAIL)) {
            $googleEmail = $googleUser->id . '@noemail.local';
        } else {
            $googleEmail = $googleUser->email;
        }

        // Cari user berdasarkan google_id
        $findUser = User::where('google_id', $googleUser->id)->first();

        if ($findUser) {
            Auth::login($findUser);

            // Pastikan personal_pelamar ada
            $this->createPersonalPelamarIfNotExists($findUser, $googleUser);
        } else {
            // Cari user berdasarkan email
            $existingUser = User::where('email', $googleEmail)->first();

            if ($existingUser) {
                $existingUser->update([
                    'name' => $googleUser->name ?? 'User Google',
                    'google_id' => $googleUser->id
                ]);
                Auth::login($existingUser);

                // Pastikan personal_pelamar ada
                $this->createPersonalPelamarIfNotExists($existingUser, $googleUser);
            } else {
                // Buat user baru
                $newUser = User::create([
                    'email' => $googleEmail,
                    'name' => $googleUser->name ?? 'User Google',
                    'google_id' => $googleUser->id,
                    'password' => Hash::make('123123123')
                ]);

                $newUser->assignRole('pelamar');

                // Pastikan personal_pelamar ada
                $this->createPersonalPelamarIfNotExists($newUser, $googleUser);

                // Kirim email verifikasi
                event(new \Illuminate\Auth\Events\Registered($newUser));

                Auth::login($newUser);
            }
        }

        $loginUser = Auth::user();

        // Redirect sesuai role
        if ($loginUser->hasRole(['pelamar'])) {
            return redirect()->intended(route('home'));
        } else if ($loginUser->hasRole(['perusahaan'])) {
            return redirect()->intended(route('dashboard-perusahaan'));
        } else if ($loginUser->hasRole(['admin'])) {
            return redirect()->intended(route('dashboard-admin'));
        } else {
            Auth::logout();
            return redirect()->route('login')->withErrors(['error' => 'Akses tidak diizinkan.']);
        }
    }

    /**
     * Membuat data personal_pelamar jika belum ada.
     */
    private function createPersonalPelamarIfNotExists($user, $googleUser)
    {
        PersonalPelamar::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name_pelamar' => $googleUser->name ?? $user->name,
                'email_pelamar' => $googleUser->email ?? $user->email,
                'phone_pelamar' => null,
                'city_pelamar' => null,
                'gender' => null,
                'date_of_birth_pelamar' => null
            ]
        );
    }
}
