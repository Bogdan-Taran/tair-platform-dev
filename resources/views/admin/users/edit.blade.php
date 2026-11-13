@extends('layouts.app')
@section('main-content')<main class="main account-form"><h1>{{ $person->exists ? 'Редактирование пользователя' : 'Новый пользователь' }}</h1>
@include('components.form-feedback')
<form method="post" action="{{ $person->exists ? route('admin.users.update',$person) : route('admin.users.store') }}">@csrf @if($person->exists) @method('put') @endif
@foreach(['firstname'=>'Имя','lastname'=>'Фамилия','patronymic'=>'Отчество','email'=>'Почта','phone'=>'Телефон','birthdate'=>'Дата рождения'] as $name=>$label)
<label for="user-{{ $name }}">{{ $label }}</label><input id="user-{{ $name }}" name="{{ $name }}" type="{{ $name==='birthdate'?'date':($name==='email'?'email':'text') }}" value="{{ old($name,$person->$name) }}" @required($name!=='patronymic')>
@endforeach
<label for="user-type">Тип профиля</label><select id="user-type" name="account_type">@foreach(['parent'=>'Родитель','athlete'=>'Взрослый спортсмен'] as $value=>$label)<option value="{{ $value }}" @selected(old('account_type',$person->account_type)===$value)>{{ $label }}</option>@endforeach</select>
<label for="user-role">Роль</label><select id="user-role" name="role">@foreach(['user'=>'Пользователь','admin'=>'Администратор'] as $value=>$label)<option value="{{ $value }}" @selected(old('role',$person->exists && $person->hasRole('admin')?'admin':'user')===$value)>{{ $label }}</option>@endforeach</select>
<button>Сохранить</button> <a href="{{ route('admin.users.index') }}">К списку</a></form>
@if($person->exists)<form method="post" action="{{ route('admin.users.invite',$person) }}">@csrf<button>Отправить ссылку установки пароля</button></form>
@if($person->id !== auth()->id())<form method="post" action="{{ route('admin.users.block',$person) }}">@csrf @method('patch')<input type="hidden" name="blocked" value="{{ $person->blocked_at?0:1 }}"><button>{{ $person->blocked_at?'Восстановить доступ':'Заблокировать доступ' }}</button></form>@endif @endif
</main>@endsection
