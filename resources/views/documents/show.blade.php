@extends('layouts.app')
@section('main-content')<main class="main account-form"><h1>{{ $document->title }}</h1>@include('components.form-feedback')
<p><a href="{{ route('documents.pdf',$document) }}">Скачать PDF</a></p><p>№ {{ $document->id }} · версия {{ $document->version }} · {{ $document->statusLabel() }}</p><p>Подтверждён {{ $document->signed_at->format('d.m.Y H:i:s') }}</p>
@if($document->expires_at)<p>Срок действия до {{ $document->expires_at->format('d.m.Y') }}</p>@endif
@php($snapshot=$document->snapshot)
<p>Подписант: {{ implode(' ', array_filter(collect($snapshot['signer'] ?? [])->only(['lastname','firstname','patronymic'])->all())) }}</p>
<p>Спортсмен: {{ implode(' ', array_filter(collect($snapshot['athlete'] ?? [])->only(['lastname','firstname','patronymic'])->all())) }}</p>
@if($snapshot['representation'] ?? null)<p>Основание представительства: {{ $snapshot['representation'] }}</p>@endif
<div class="document-body">{{ $snapshot['body'] ?? '' }}</div>
@if($snapshot['passport'] ?? null)<h2>Реквизиты подписанта</h2>@foreach($snapshot['passport'] as $value)<p>{{ $value }}</p>@endforeach @endif
@if($document->revoked_at)<p>Прекращён {{ $document->revoked_at->format('d.m.Y H:i') }}: {{ $document->revocation_reason }}</p>
@elseif($document->kind==='consent' || auth()->user()->hasRole('admin'))<form method="post" action="{{ route('documents.revoke',$document) }}">@csrf @method('patch')<label for="reason">Причина {{ $document->kind==='consent'?'отзыва согласия':'прекращения договора' }}</label><textarea id="reason" name="reason" required maxlength="1000"></textarea><button>{{ $document->kind==='consent'?'Отозвать согласие':'Прекратить договор' }}</button></form>@endif
<a href="{{ route('documents') }}">Все документы</a></main>@endsection
