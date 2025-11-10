{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">Accedi</h1>
        <p class="mt-1 text-sm text-gray-600">Entra per gestire le tue prenotazioni</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full pr-10"
                          type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500"
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ricordami') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-amber-700 hover:text-amber-800 font-medium"
                   href="{{ route('password.request') }}">
                    {{ __('Password dimenticata?') }}
                </a>
            @endif
        </div>

        <div class="pt-1">
            <x-primary-button class="w-full justify-center bg-amber-600 hover:bg-amber-700">
                {{ __('Accedi') }}
            </x-primary-button>
        </div>

        <p class="text-center text-sm text-gray-600">
            {{ __('Non hai un account?') }}
            <a href="{{ route('register') }}" class="font-semibold text-amber-700 hover:text-amber-800">
                {{ __('Registrati') }}
            </a>
        </p>
    </form>
</x-guest-layout>
