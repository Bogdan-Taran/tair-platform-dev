<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/style-contracts-main.css') }}">

    <link rel="stylesheet" href="{{ url('frontend/css/styles.css')}}">
    <link rel="stylesheet" href="{{ url('frontend/css/modal/style-contact-us.css')}}">


    @stack('styles')

    <!-- Scripts -->
    {{--    @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
</head>

<body class="">
@include('layouts.navigation')

@yield('main-content')


@include('layouts.footer')

<script src="{{ url('frontend/js/scripts.js')}}" defer></script>

@stack('scripts')


</body>
</html>
