@php
    $titles = ['login' => 'Платформа ТАИР', 'register' => 'Регистрация', 'forgot' => 'Восстановление пароля', 'reset' => 'Новый пароль'];
    $actions = ['login' => 'login', 'register' => 'register', 'forgot' => 'password.email', 'reset' => 'password.store'];
    $buttons = ['login' => 'Войти', 'register' => 'Зарегистрироваться', 'forgot' => 'Отправить ссылку', 'reset' => 'Сохранить пароль'];
    $prefix = ($modal ?? false) ? 'modal-'.$mode : 'page-'.$mode;
    $showErrors = !($modal ?? false) || old('_auth_form') === $mode;
    $fields = $mode === 'register' ? ['firstname' => ['Имя', 'text', 'given-name'], 'lastname' => ['Фамилия', 'text', 'family-name'], 'patronymic' => ['Отчество', 'text', 'additional-name'], 'email' => ['Почта', 'email', 'email'], 'phone' => ['Номер телефона', 'tel', 'tel'], 'birthdate' => ['Дата рождения', 'date', 'bday']] : ['email' => ['Почта', 'email', 'username']];
    if ($mode !== 'forgot') { $fields['password'] = ['Пароль', 'password', $mode === 'login' ? 'current-password' : 'new-password']; }
    if (in_array($mode, ['register', 'reset'])) { $fields['password_confirmation'] = ['Повторите пароль', 'password', 'new-password']; }
@endphp
<section class="auth-panel auth-panel--{{ $mode }}" aria-labelledby="{{ $prefix }}-title">
    @if($modal ?? false)<button class="auth-close" type="button" data-auth-close aria-label="Закрыть">×</button>@else<a class="auth-close" href="{{ route('index') }}" aria-label="На главную">×</a>@endif
    <div class="auth-photo"><img src="{{ asset('frontend/src/img/auth/'.($mode === 'register' ? 'register-modal-image.jpg' : 'login-modal-image.jpg')) }}" alt="Тренировка в клубе ТАИР"></div>
    <div class="auth-content">
        <h1 id="{{ $prefix }}-title">{{ $titles[$mode] }}</h1>
        @if($mode === 'forgot')<p>Введите почту, указанную при регистрации. Мы отправим ссылку для смены пароля.</p>@endif
        @if($mode === 'reset')<p>Придумайте новый пароль длиной не менее 8 символов.</p>@endif
        <form method="post" action="{{ route($actions[$mode]) }}" data-auth-form>
            @csrf
            <input type="hidden" name="_auth_form" value="{{ $mode }}">
            @if($mode === 'reset')<input type="hidden" name="token" value="{{ request()->route('token') }}">@endif
            <p class="auth-feedback" role="status" aria-live="polite" data-auth-feedback @if(!session('status')) hidden @endif>{{ session('status') }}</p>
            @foreach($fields as $name => [$label, $type, $autocomplete])
                <div class="auth-field">
                    <label for="{{ $prefix }}-{{ $name }}">{{ $label }}</label>
                    <input id="{{ $prefix }}-{{ $name }}" name="{{ $name }}" type="{{ $type }}" autocomplete="{{ $autocomplete }}" placeholder="{{ $label }}" required
                        @if($type !== 'password') value="{{ $showErrors ? old($name, $mode === 'reset' && $name === 'email' ? request()->query('email') : '') : '' }}" @endif
                        @if($name === 'birthdate') max="{{ now()->subDay()->toDateString() }}" @endif
                        @if($name === 'phone') maxlength="20" @endif
                        @if($type === 'password' && $mode !== 'login') minlength="8" @endif
                        aria-describedby="{{ $prefix }}-error-{{ $name }}">
                    <span class="auth-field-error" id="{{ $prefix }}-error-{{ $name }}" data-error-for="{{ $name }}">{{ $showErrors ? $errors->first($name) : '' }}</span>
                </div>
            @endforeach
            @if($mode === 'login')
                <div class="auth-options"><label><input type="checkbox" name="remember" value="1"> Запомнить меня</label><a href="{{ route('password.request') }}" data-auth-open="forgot">Забыли пароль?</a></div>
            @elseif($mode === 'register')
                <label class="auth-agreement"><input type="checkbox" name="agreement" value="1" required @checked(old('agreement'))> Я согласен на обработку персональных данных</label>
                <span class="auth-field-error" data-error-for="agreement">{{ $showErrors ? $errors->first('agreement') : '' }}</span>
            @endif
            <button type="submit" class="auth-submit">{{ $buttons[$mode] }}</button>
        </form>
        <div class="auth-switch">
            @if($mode === 'login')<span>Нет аккаунта?</span><a href="{{ route('register') }}" data-auth-open="register">Зарегистрироваться</a>
            @else<span>{{ $mode === 'register' ? 'Есть аккаунт?' : 'Вспомнили пароль?' }}</span><a href="{{ route('login') }}" data-auth-open="login">Войти</a>@endif
        </div>
    </div>
</section>
