<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="UTF-8">

        <title>ТАИР - Спортивный клуб</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('frontend/css/style-contracts-main.css') }}">

        <script src="{{ url('frontend/js/import-footer-header.js')}}" defer></script>

        <link rel="stylesheet" href="{{ url('frontend/css/styles.css')}}">
        <link rel="stylesheet" href="{{ url('frontend/css/modal/style-contact-us.css')}}">

        <!--  стили reviews -->
        <link rel="stylesheet" href="{{ url('frontend/css/style-reviews.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ url('frontend/slick/slick.css')}}"/>
        <link rel="stylesheet" type="text/css" href="{{ url('frontend/slick/slick-theme.css')}}"/>
        <link rel="stylesheet" type="text/css" href="{{ url('frontend/css/style-schedule.css')}}"/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        <div>
            @include('layouts.navigation')

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
