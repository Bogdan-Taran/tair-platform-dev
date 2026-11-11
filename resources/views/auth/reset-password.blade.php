@extends('layouts.auth')
@section('title', 'Новый пароль — ТАИР')
@section('auth-content')
@include('components.auth-panel', ['mode' => 'reset', 'modal' => false])
@endsection
