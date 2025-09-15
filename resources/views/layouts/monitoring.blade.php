<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TalentDNA Corporate') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
    
    class="font-sans text-gray-900 antialiased bg-center bg-scroll"
    {{-- @if ($appreance->color_secondary == NULL) --}}
    style="background-image: url('{{ asset('img/login.png') }}');"
    {{-- @else --}}
    {{-- style="background-color: {{ $appreance->color_secondary }}" --}}
    {{-- @endif --}}
    >
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 ">
                {{ $slot }}
        </div>
    </body>
</html>
