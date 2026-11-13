@extends('layouts.app')
@section('main-content')
<main class="main account-form"><h1>{{ $child->exists ? 'Профиль ребёнка' : 'Добавление ребёнка' }}</h1>
@include('components.form-feedback')
<form method="post" action="{{ $child->exists ? route('children.update',$child) : route('store-child') }}">
@csrf @if($child->exists) @method('patch') @endif
@foreach(['firstname'=>'Имя','lastname'=>'Фамилия','patronymic'=>'Отчество'] as $name=>$label)
<label for="child-{{ $name }}">{{ $label }}</label><input id="child-{{ $name }}" name="{{ $name }}" value="{{ old($name,$child->$name) }}" @required($name !== 'patronymic') maxlength="255">
@endforeach
<label for="child-birthdate">Дата рождения</label><input id="child-birthdate" type="date" name="birthdate" value="{{ old('birthdate',$child->birthdate) }}" max="{{ now()->subDay()->toDateString() }}" required>
<label for="child-branch">Филиал</label><select id="child-branch" name="branch_id" required><option value="">Выберите филиал</option>
@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected(old('branch_id',$child->branch_id)==$branch->id)>{{ $branch->branch_name }}</option>@endforeach</select>
@if(!$child->archived_at)<button type="submit">Сохранить</button>@endif <a href="{{ route('dashboard') }}">Вернуться в кабинет</a>
</form>
@if($child->exists)<form method="post" action="{{ route('children.archive',$child) }}">@csrf @method('patch')<input type="hidden" name="archived" value="{{ $child->archived_at ? 0 : 1 }}"><button>{{ $child->archived_at ? 'Восстановить профиль' : 'Переместить в архив' }}</button></form>@endif
</main>@endsection
