@extends('layouts.app')
@section('main-content')<main class="main account-form"><h1>Версии документов</h1>@include('components.form-feedback')
<p>Опубликованный текст не изменяется. Для изменений создайте новую версию. Публикуйте только утверждённые клубом документы.</p>
<form method="post" action="{{ route('admin.templates.store') }}">@csrf
<label for="kind">Вид</label><select name="kind" id="kind"><option value="contract">Договор</option><option value="consent" @selected(old('kind')==='consent')>Согласие на ПДн</option></select>
<label for="version">Номер версии</label><input id="version" name="version" value="{{ old('version') }}" required maxlength="80">
<label for="title">Название</label><input id="title" name="title" value="{{ old('title') }}" required maxlength="255">
<label for="body">Полный утверждённый текст</label><textarea id="body" name="body" rows="16" required>{{ old('body') }}</textarea>
<label for="validity-mode">Правило срока</label><select id="validity-mode" name="validity_mode"><option value="days">Количество дней</option><option value="month_end">До конца месяца</option><option value="none">Без фиксированного срока</option></select><label for="valid-days">Срок договора в днях</label><input id="valid-days" type="number" name="valid_days" value="{{ old('valid_days') }}" min="1" max="3650"><button>Сохранить черновик</button></form>
@foreach($templates as $template)<article class="account-record"><h2>{{ $template->title }} · {{ $template->version }}</h2><details><summary>Полный текст</summary><div class="document-body">{{ $template->body }}</div></details>
@if($template->published_at)<p>Опубликована {{ $template->published_at->format('d.m.Y H:i') }}</p>@else<a href="{{ route('admin.templates.edit',$template) }}">Редактировать черновик</a><form method="post" action="{{ route('admin.templates.publish',$template) }}">@csrf<label><input type="checkbox" name="approved" value="1" required> Текст утверждён клубом</label><button>Опубликовать</button></form>@endif</article>@endforeach {{ $templates->links() }}
</main>@endsection
