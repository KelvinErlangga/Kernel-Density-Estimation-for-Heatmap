@extends('layouts.master')

@section('title', 'Login | CVRE GENERATE')

@section('content')
<!-- Login Form -->
<div class="min-h-screen flex items-center justify-center relative">
    <!-- Background Image -->
    <img src="{{asset('assets/homepage/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md relative z-10">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <a href="/">
                <img src="{{asset('assets/homepage/logo.png')}}" alt="Logo" style="width: 180px; height: auto" />
            </a>
        </div>

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                    style="height: 45px; line-height: 45px; padding: 0 16px"
                    value="{{old('email')}}" />
                @error('email')
                <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                    style="height: 45px; line-height: 45px; padding: 0 16px" />
            </div>

            <!-- Lupa Password dan Ingat Saya -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                </div>
                <div class="text-sm text-gray-800">
                    Lupa Password?
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-blue-500 hover:underline focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"> Klik di sini </a>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    style="background-color: #3538cd; color: white; padding: 12px 16px; border-radius: 5px; width: 100%; border: none; transition: background-color 0.3s"
                    onmouseover="this.style.backgroundColor='#2727a1';"
                    onmouseout="this.style.backgroundColor='#3538cd';">
                    Masuk
                </button>
            </div>

            <!-- Divider -->
            <div class="flex items-center my-6">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="px-4 text-gray-500">Atau</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <!-- Login with Google -->
            <div class="flex justify-center mb-6">
                <a href="{{route('auth.google')}}" class="flex items-center border border-gray-300 bg-white text-gray-700 px-4 py-2 rounded-md shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google Logo" class="h-5 w-5 mr-3" />
                    Masuk dengan Google
                </a>
            </div>

            <!-- Belum Punya Akun -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{route('register')}}" class="text-blue-500 hover:underline">Klik di sini</a>
                </p>
            </div>
        </form>
    </div>
</div>

@endsection