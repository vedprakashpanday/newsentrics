<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Newsentric'))</title>
        <meta name="description" content="@yield('meta_description', 'Get the latest and trending news from around the world.')">
        <meta name="keywords" content="@yield('meta_keywords', 'news, latest news, world news, trending')">
        
        <meta property="og:title" content="@yield('title', config('app.name', 'Newsentric'))">
        <meta property="og:description" content="@yield('meta_description', 'Get the latest and trending news.')">
        <meta property="og:image" content="@yield('meta_image', asset('default-logo.png'))"> <meta property="og:type" content="article">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>