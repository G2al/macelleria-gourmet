{{-- resources/views/components/application-logo.blade.php --}}
@props(['class' => 'h-16 w-auto'])

<img src="{{ asset('images/polleria-gourmet-logo.svg') }}"
     alt="Polleria Gourmet"
     class="{{ $class }}"
     loading="eager">
