<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Личный кабинет — ТАИР</title>
<link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}"><link rel="stylesheet" href="{{ asset('frontend/css/auth-forms.css') }}"></head>
<body class="auth-page"><main class="auth-page-main"><section class="auth-panel auth-panel--simple"><div class="auth-content"><a href="{{ route('index') }}">ТАИР · На главную</a>{{ $slot }}</div></section></main></body></html>
