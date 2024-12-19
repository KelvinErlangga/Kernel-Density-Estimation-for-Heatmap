@extends('layouts.master')

@section('title', 'Verifikasi Email | CVRE GENERATE')

@section('content')
<!-- Login Form -->
<div class="min-h-screen flex items-center justify-center relative">
    <!-- Background Image -->
    <img src="{{asset('assets/images/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md relative z-20">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="{{asset('assets/homepage/logo.png')}}" alt="Logo" class="w-44 h-auto" />
        </div>

        <!-- Verifikasi Email Message -->
        <div class="text-center mb-6">
            <h2 class="text-gray-800 font-semibold text-xs mb-6">
                Masukkan alamat email yang anda daftarkan di CVRE Generate ini, pastikan alamat email yang dimasukkan valid
            </h2>
        </div>

        @if (session('status'))
        <div class="text-sm font-thin text-green-500">Link reset password sudah terkirim</div>
        @endif

        @error('email')
        <div class="text-sm font-thin text-red-500">Email tidak terdaftar</div>
        @enderror

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="sr-only">Alamat Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{old('email')}}"
                    class="w-full px-4 py-3 mb-4 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300 text-sm"
                    placeholder="Alamat Email"
                    required />
            </div>
            <div>
                <button
                    type="submit"
                    style="background-color: #3538cd; color: white; padding: 12px 16px; border-radius: 5px; width: 100%; border: none; transition: background-color 0.3s"
                    onmouseover="this.style.backgroundColor='#2727a1';"
                    onmouseout="this.style.backgroundColor='#3538cd';">
                    Kirim Email Reset Password
                </button>
            </div>
        </form>
    </div>
</div>


@endsection