@extends('layouts.app')
@section('main-content')<main class="main account-form"><h1>Управление пользователями</h1>
@include('components.form-feedback')
<p><a href="{{ route('admin.templates.index') }}">Версии документов</a> · <a href="{{ route('admin.users.create') }}">Создать пользователя</a> · <a href="{{ route('admin.dashboard') }}">Реестры</a> · <a href="{{ route('admin.audit') }}">Журнал действий</a></p>
<form method="get"><label for="user-search">Имя, почта или телефон</label><input id="user-search" name="q" value="{{ $q }}"><button>Найти</button></form>
@forelse($users as $person)<article class="account-record"><h2>{{ $person->lastname }} {{ $person->firstname }}</h2><p>{{ $person->email }} · {{ $person->phone }}</p><p>{{ $person->blocked_at ? 'Заблокирован' : 'Доступ открыт' }} · {{ $person->hasRole('admin') ? 'Администратор' : 'Пользователь' }}</p><a href="{{ route('admin.users.edit',$person) }}">Редактировать</a></article>@empty<p>Пользователи не найдены.</p>@endforelse
{{ $users->links() }}</main>@endsection
