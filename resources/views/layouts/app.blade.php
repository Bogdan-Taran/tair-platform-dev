<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">

    <title>@yield('title', 'Платформа ТАИР')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/style-contracts-main.css') }}">

    <link rel="stylesheet" href="{{ url('frontend/css/auth-modals.css')}}">
    <link rel="stylesheet" href="{{ url('frontend/css/styles.css')}}">
    <link rel="stylesheet" href="{{ url('frontend/css/modal/style-contact-us.css')}}">
    <link rel="stylesheet" href="{{ url('frontend/css/modal/custom-alert.css')}}">


    @auth
        <link rel="stylesheet" href="{{ url('frontend/css/style-dashboard.css')}}">
    @endauth


    <link rel="stylesheet" href="{{ asset('frontend/css/auth-forms.css') }}">
    @stack('styles')

    <!-- Scripts -->
    {{--    @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
</head>

<body class="">
@include('layouts.navigation')

@auth
    <div class="sidenav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Профиль</a>
        <a href="{{ route('documents') }}" class="{{ request()->routeIs('documents') ? 'active' : '' }}">Документы</a>
    </div>
@endauth

@guest
@include('components.register-modal')
@include('components.login-modal')
@include('components.forgot-password-modal')
@endguest

@yield('main-content')
@isset($slot)<main class="main account-form">{{ $slot }}</main>@endisset


@include('layouts.footer')



<script src="{{ url('frontend/js/scripts.js')}}" defer></script>
<script src="{{ url('frontend/js/auth-modal.js')}}" defer></script>

<script src="{{ asset('frontend/js/navigation.js') }}" defer></script>





@stack('scripts')


</body>
</html>
