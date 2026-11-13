@extends('layouts.app')
@section('main-content')
    <main class="main account-form"><h1>Все документы</h1>@include('components.form-feedback')
        <h2>Ознакомление и подписание</h2>@forelse($templates as $template)
            <p><a href="{{ route('documents.preview',$template) }}">{{ $template->title }} ·
                    версия {{ $template->version }}</a></p>
        @empty
            <p>Клуб ещё не опубликовал документы для подписания.</p>
        @endforelse
        <h2>История документов</h2>@forelse($documents as $document)
            <article class="account-record"><h3><a
                        href="{{ route('documents.show',$document) }}">{{ $document->title }}</a></h3>
                <p>{{ $document->signed_at->format('d.m.Y H:i') }} · {{ $document->statusLabel() }} ·
                    версия {{ $document->version }}</p></article>
        @empty
            <p>Подтверждённых экземпляров пока нет.</p>
        @endforelse {{ $documents->links() }}
        @foreach($legacyContracts as $contract)
            <article class="account-record"><h3>Договор № {{ $contract->id }}</h3>
                <p>{{ \Carbon\Carbon::parse($contract->sign_date)->format('d.m.Y H:i') }}</p>
                <p>Историческая запись. Текст подписанного экземпляра ранее не сохранялся. Для получения копии
                    обратитесь в клуб.</p></article>
        @endforeach
        <a href="{{ route('dashboard') }}">В кабинет</a></main>
@endsection
