@extends('layouts.auth')
@section('title', 'Вход — ТАИР')
@section('auth-content')
@include('components.auth-panel', ['mode' => 'login', 'modal' => false])
@endsection
