<div class="modal-backdrop" id="loginModal" style="display: none">
    <div class="modal-content">
        <button class="close-button" type="button" id="closeModalButton" onclick="closeModal()">
            <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <line x1="1" y1="19.5858" x2="19.5858" y2="1" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <line x1="1" y1="-1" x2="27.2843" y2="-1"
                      transform="matrix(-0.707107 -0.707107 -0.707107 0.707107 21 21)" stroke="white" stroke-width="2"
                      stroke-linecap="round"/>
            </svg>
        </button>


        <div class="modal-photo-wrapper">
            <img src="{{ url('frontend/src/img/auth/login-modal-image.jpg') }}" alt="Фото бойца на модальном окне"
                 class="modal-left-content modal-left-full-fill">
        </div>

        <div class="modal-right-content">

            <h2>Платформа ТАИР</h2>
            <div class="modal-output-errors" id="modalErrors"></div>

            <div class="modal-output-errors" id="modalErrors"></div>

            <form id="loginForm" action="{{ route('login') }}" method="POST" class="login-form">
                @csrf

                <div class="form-group">
                    <input type="text" id="reg_email" name="email" required placeholder="Email почта">
                    <span class="field-error" id="error_email"></span>
                </div>
                <div class="form-group">
                    <input type="password" id="reg_password" name="password" required placeholder="Пароль">
                    <span class="field-error" id="error_password"></span>
                </div>
                <a href="{{ route('password.request') }}">Забыли пароль?</a>

                <button type="submit" class="cta-button btn-submit">Войти</button>
            </form>

            <div class="modal-bottom-text">
                <p>Нет аккаунта?</p>
                <a onclick="openRegisterModal()">Зарегистрироваться</a>
            </div>


        </div>

    </div>

</div>


