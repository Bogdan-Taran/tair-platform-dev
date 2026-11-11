<div class="auth-overlay" id="forgotPasswordModal" data-auth-modal="forgot" role="dialog" aria-modal="true" aria-label="Личный кабинет ТАИР" @if(old('_auth_form') !== 'forgot') hidden @endif>
    @include('components.auth-panel', ['mode' => 'forgot', 'modal' => true])
</div>
