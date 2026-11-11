<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Личный кабинет — ТАИР')</title>
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/auth-forms.css') }}">
</head>
<body class="auth-page">
    <main class="auth-page-main">@yield('auth-content')</main>
    <script src="{{ asset('frontend/js/auth-modal.js') }}" defer></script>
</body>
</html>
