<section><h1>Личные данные</h1>@include('components.form-feedback')
<form method="post" action="{{ route('profile.update') }}">@csrf @method('patch')
@foreach(['firstname'=>'Имя','lastname'=>'Фамилия','patronymic'=>'Отчество','email'=>'Почта','phone'=>'Телефон','birthdate'=>'Дата рождения'] as $name=>$label)
<label for="profile-{{ $name }}">{{ $label }}</label><input id="profile-{{ $name }}" name="{{ $name }}" type="{{ $name==='birthdate'?'date':($name==='email'?'email':'text') }}" value="{{ old($name,$user->$name) }}" @required($name!=='patronymic')>
@endforeach
<label for="profile-type">Тип профиля</label><select id="profile-type" name="account_type"><option value="parent">Родитель</option><option value="athlete" @selected(old('account_type',$user->account_type)==='athlete')>Совершеннолетний спортсмен</option></select>
<button>Сохранить</button><a href="{{ route('dashboard') }}">В кабинет</a></form>
@if($user->pending_email)<p>Ожидает подтверждения: {{ $user->pending_email }}</p><form method="post" action="{{ route('profile.email.resend') }}">@csrf<button>Повторить письмо подтверждения</button></form>@endif
<h2>Фото профиля</h2>@if($user->avatar_path)<img class="account-avatar" src="{{ route('avatar.show') }}" alt="Ваш аватар">@endif
<form method="post" action="{{ route('avatar.store') }}" enctype="multipart/form-data">@csrf<label for="avatar">JPEG, PNG или WebP до 5 МБ</label><input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required><button>Обновить фото</button></form>
@if($user->avatar_path)<form method="post" action="{{ route('avatar.destroy') }}">@csrf @method('delete')<button>Удалить фото</button></form>@endif
</section>
