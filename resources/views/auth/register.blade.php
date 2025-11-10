{{-- resources/views/auth/register.blade.php --}}
<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">Crea un account</h1>
        <p class="mt-1 text-sm text-gray-600">Prenota facilmente i tuoi prodotti freschi</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                          :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Surname (opzionale: se nel tuo users c’è) -->
        @if (Schema::hasColumn('users', 'surname'))
            <div>
                <x-input-label for="surname" :value="__('Cognome')" />
                <x-text-input id="surname" class="block mt-1 w-full" type="text" name="surname"
                              :value="old('surname')" autocomplete="family-name" />
                <x-input-error :messages="$errors->get('surname')" class="mt-2" />
            </div>
        @endif

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Conferma password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-1">
            <x-primary-button class="w-full justify-center bg-amber-600 hover:bg-amber-700">
                {{ __('Registrati') }}
            </x-primary-button>
        </div>

        <p class="text-center text-sm text-gray-600">
            {{ __('Hai già un account?') }}
            <a href="{{ route('login') }}" class="font-semibold text-amber-700 hover:text-amber-800">
                {{ __('Accedi') }}
            </a>
        </p>
    </form>
</x-guest-layout>
