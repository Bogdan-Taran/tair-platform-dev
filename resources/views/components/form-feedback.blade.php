@if(session('status'))<p role="status">{{ session('status') === 'profile-updated' ? 'Профиль сохранён.' : session('status') }}</p>@endif
@if($errors->any())<div role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
