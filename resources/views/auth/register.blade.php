@extends('layouts.auth')
@section('title', 'Регистрация — ТАИР')
@section('auth-content')
@include('components.auth-panel', ['mode' => 'register', 'modal' => false])
@endsection
