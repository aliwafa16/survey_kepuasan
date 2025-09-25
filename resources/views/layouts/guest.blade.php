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

<body class="font-sans text-gray-900 antialiased bg-center bg-no-repeat min-h-screen"
    style="background-image: url('{{ asset('assets/images/5315093.jpg') }}'); background-size: cover;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 ">
        <div>
            <a href="/">
                {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
            </a>
        </div>

        <div class="w-full sm:max-w-md text-center">
            <p class="text-slate-600 text-2xl font-bold">{{ Str::replace('-', ' ', env('APP_NAME_FULL')) }}</p>
            <p class="text-slate-600 text-sm">{{ Str::replace('-', ' ', env('APP_NAME_DESC')) }}</p>
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-[#0075FF]/14 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
