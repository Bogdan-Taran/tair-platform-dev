@extends('layouts.app')

@section('main-content')
    <main class="main-documents">
        <h2>Договор-оферта и Правила оказания услуг в спортивном клубе “ТАИР”</h2>
        <p>Чтобы ознакомиться и подписать договор, нажмите кнопку ниже, он появится здесь после подписания</p>
        <a class="button-to-sign-contract" href="{{ route('sign-contract') }}">К договору</a>
    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('frontend/css/style-sign-contract.css')}}">
@endpush
