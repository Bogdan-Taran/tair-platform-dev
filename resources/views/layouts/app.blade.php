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

    <link rel="stylesheet" href="{{ url('frontend/css/auth-modals.css')}}">
    <link rel="stylesheet" href="{{ url('frontend/css/styles.css')}}">
    <link rel="stylesheet" href="{{ url('frontend/css/modal/style-contact-us.css')}}">


    @stack('styles')

    <!-- Scripts -->
    {{--    @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
</head>

<body class="">
@include('layouts.navigation')
@include('components.register-modal')
@include('components.login-modal')

@yield('main-content')


@include('layouts.footer')

<script src="{{ url('frontend/js/scripts.js')}}" defer></script>
<script src="{{ url('frontend/js/auth-modal.js')}}" defer></script>

<script defer>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Находим элементы на странице
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        const navLinks = document.querySelectorAll('.main-nav a');

        // 2. Проверяем, что элементы существуют (чтобы не было ошибок на других страницах)
        if (menuToggle && mainNav) {

            // Клик по кнопке-бургеру (открыть/закрыть)
            menuToggle.addEventListener('click', function () {
                menuToggle.classList.toggle('active');
                mainNav.classList.toggle('active');
            });

            // Клик по любой ссылке в меню (закрыть меню при переходе)
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    menuToggle.classList.remove('active');
                    mainNav.classList.remove('active');
                });
            });

            // Клик в любом месте экрана вне меню (закрыть меню)
            document.addEventListener('click', function (event) {
                if (!mainNav.contains(event.target) && !menuToggle.contains(event.target)) {
                    menuToggle.classList.remove('active');
                    mainNav.classList.remove('active');
                }
            });
        }
    });

</script>



@stack('scripts')


</body>
</html>
