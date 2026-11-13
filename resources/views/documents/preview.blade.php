@extends('layouts.app')
@section('main-content')<main class="main account-form"><h1>{{ $template->title }}</h1><p>Версия {{ $template->version }}</p>@include('components.form-feedback')
<form method="get" action="{{ route('documents.preview',$template) }}"><label for="document-athlete">Документ для</label><select id="document-athlete" name="child_id"><option value="">Себя</option>@foreach($children as $item)<option value="{{ $item->id }}" @selected($child?->id===$item->id)>{{ $item->lastname }} {{ $item->firstname }} · {{ $item->branch?->branch_name }}</option>@endforeach</select>
<label for="representation">Основание представительства при оформлении за ребёнка</label><input id="representation" name="representation" value="{{ $representation }}" maxlength="500" placeholder="Например, родитель"><button>Обновить предварительный просмотр</button></form>
<p>Подписант: {{ auth()->user()->lastname }} {{ auth()->user()->firstname }} {{ auth()->user()->patronymic }}</p>
@if($template->kind==='contract')<p><a href="{{ route('passport.edit') }}">Проверить паспортные данные</a></p>@endif
<div class="document-body">{{ $snapshot['body'] }}</div>
@if(config('documents.signing_enabled'))<form method="post" action="{{ route('documents.sign',$template) }}">@csrf
<input type="hidden" name="child_id" value="{{ $child?->id }}"><input type="hidden" name="representation" value="{{ $representation }}"><input type="hidden" name="preview_checksum" value="{{ $checksum }}">
<label><input type="checkbox" name="agreement" value="1" required> Я ознакомился с полным текстом, проверил данные и подтверждаю согласие с указанной версией</label><button>Подтвердить документ</button></form>
@else<p>Подписание временно недоступно.</p>@endif
<a href="{{ route('documents') }}">Все документы</a></main>@endsection
