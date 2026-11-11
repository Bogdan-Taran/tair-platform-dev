@extends('layouts.app')
@section('title', 'Паспортные данные — ТАИР')
@section('main-content')
<main class="main">
    <h1 class="block-header">Паспортные данные</h1>
    <form action="{{ route('passport.update') }}" method="post" class="account-form">
        @csrf
        @foreach(['series' => 'Серия', 'number' => 'Номер', 'issue_date' => 'Дата выдачи', 'issue_by' => 'Кем выдан', 'address' => 'Адрес регистрации'] as $field => $label)
            <label for="passport-{{ $field }}">{{ $label }}</label>
            <input id="passport-{{ $field }}" name="{{ $field }}" type="{{ $field === 'issue_date' ? 'date' : 'text' }}" value="{{ old($field, $passport?->$field) }}" required>
            <x-input-error :messages="$errors->get($field)" />
        @endforeach
        <button type="submit">Сохранить</button>
        <a href="{{ route('dashboard') }}">В кабинет</a>
    </form>
</main>
@endsection
