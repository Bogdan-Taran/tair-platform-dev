@extends('layouts.app')

@section('main-content')
    <main class="main-documents">
        <div class="empty-documents-block">
            <h2>Пока у вас нет подписанных договоров об оказании платных услуг</h2>
            <p>Чтобы ознакомиться и подписать договор, нажмите кнопку ниже, он появится здесь после подписания</p>
            <a class="button-to-sign-contract" href="{{ route('sign-contract') }}">К договору</a>
        </div>
    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('frontend/css/style-documents.css')}}">
@endpush
