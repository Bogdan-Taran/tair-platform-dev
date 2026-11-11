<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>@yield('code') — @yield('message') · ТАИР</title><link rel="stylesheet" href="{{ asset('frontend/css/error-pages.css') }}"></head>
<body class="error-page"><main>
    <div class="error-scene">
        <div class="error-figure"><img src="{{ asset('frontend/src/img/errors/access-denied.png') }}" alt=""></div>
        <div class="error-shadow" aria-hidden="true"></div>
        <h1>@yield('code')</h1>
        <p>@yield('message')</p>
    </div>
    <nav class="error-actions" aria-label="Продолжить работу"><a href="{{ url('/') }}">На главную</a><a href="{{ url('/dashboard') }}">В личный кабинет</a></nav>
</main></body></html>
