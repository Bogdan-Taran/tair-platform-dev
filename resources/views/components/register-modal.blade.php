<div class="auth-overlay" id="registerModal" data-auth-modal="register" role="dialog" aria-modal="true" aria-label="Личный кабинет ТАИР" @if(old('_auth_form') !== 'register') hidden @endif>
    @include('components.auth-panel', ['mode' => 'register', 'modal' => true])
</div>
