<div class="auth-overlay" id="loginModal" data-auth-modal="login" role="dialog" aria-modal="true" aria-label="Личный кабинет ТАИР" @if(old('_auth_form') !== 'login') hidden @endif>
    @include('components.auth-panel', ['mode' => 'login', 'modal' => true])
</div>
