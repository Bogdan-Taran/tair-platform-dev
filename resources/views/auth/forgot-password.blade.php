@extends('layouts.auth')
@section('title', 'Восстановление пароля — ТАИР')
@section('auth-content')
@include('components.auth-panel', ['mode' => 'forgot', 'modal' => false])
@endsection
