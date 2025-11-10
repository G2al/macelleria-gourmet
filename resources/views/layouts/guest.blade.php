{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Polleria Gourmet') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-cover bg-center bg-fixed"
         style="background-image: url('{{ asset('images/background.jpg') }}');">
        {{-- overlay per leggibilità --}}
        <div class="min-h-screen bg-white/85">
            <div class="min-h-screen flex flex-col items-center justify-center px-6">
                <div class="flex flex-col items-center gap-4">
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="h-16 w-auto" />
                        <span class="sr-only">{{ config('app.name', 'Polleria Gourmet') }}</span>
                    </a>
                    <p class="text-sm text-amber-700/90 font-medium tracking-wide">
                        Benvenuto nell’area clienti Polleria Gourmet
                    </p>
                </div>

                <div class="mt-6 w-full sm:max-w-md bg-white/95 backdrop-blur-sm border border-amber-100 shadow-xl rounded-2xl p-6 sm:p-8">
                    {{ $slot }}
                </div>

                {{-- Footer brand piccolo --}}
                <div class="mt-6 text-xs text-gray-500">
                    © {{ date('Y') }} Polleria Gourmet — Tutti i diritti riservati
                </div>
            </div>
        </div>
    </div>
</body>
</html>
