<div class="modal-backdrop" id="registerModal" style="display: none">
    <div class="modal-content" style="padding: 0; ">
        <button class="close-button" type="button" id="closeModalButton" onclick="closeModal()">
            <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <line x1="1" y1="19.5858" x2="19.5858" y2="1" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <line x1="1" y1="-1" x2="27.2843" y2="-1"
                      transform="matrix(-0.707107 -0.707107 -0.707107 0.707107 21 21)" stroke="white" stroke-width="2"
                      stroke-linecap="round"/>
            </svg>
        </button>

        <div class="modal-photo-wrapper" style="border-radius: 0">
            <img src="{{ url('frontend/src/img/auth/register-modal-image.jpg') }}" alt="Фото бойца на модальном окне"
                 class="modal-left-content modal-left-full-fill" style="clip-path: none">
        </div>

        <div class="modal-right-content">

            <h2 id="modal-reg-h2">РЕГИСТРАЦИЯ</h2>
            <div class="modal-output-errors" id="modalErrors"></div>

            <form id="registerForm" action="{{ route('register') }}" method="POST">
                @csrf

                <div class="form-group">
                    <input type="text" id="reg_firstname" name="firstname" required placeholder="Имя">
                    <span class="field-error" id="error_firstname"></span>
                </div>
                <div class="form-group">
                    <input type="text" id="reg_lastname" name="lastname" required placeholder="Фамилия">
                    <span class="field-error" id="error_lastname"></span>
                </div>
                <div class="form-group">
                    <input type="text" id="reg_patronymic" name="patronymic" required placeholder="Отчество">
                    <span class="field-error" id="error_patronymic"></span>
                </div>
                <div class="form-group">
                    <input type="text" id="reg_email" name="email" required placeholder="Почта">
                    <span class="field-error" id="error_email"></span>
                </div>
                <div class="form-group">
                    <input type="tel" id="reg_phone" name="phone" required placeholder="Номер телефона">
                    <span class="field-error" id="error_phone"></span>
                </div>
                <div class="form-group">
                    <input type="date" id="reg_birthdate" name="birthdate" required placeholder="Дата рождения">
                    <span class="field-error" id="error_birthdate"></span>
                </div>

                <div class="form-group">
                    <input type="password" id="reg_password" name="password" required placeholder="Пароль">
                    <span class="field-error" id="error_password"></span>
                </div>

                <div class="form-group">
                    <input type="password" id="reg_password_confirmation" name="password_confirmation" required
                           placeholder="Подтверждение пароля">
                </div>
                <button type="submit" class="btn-submit">Зарегистрироваться</button>
            </form>
            <div class="modal-bottom-text" style="margin-top: .6rem">
                <p>Уже есть аккаунт?</p>
                <a onclick="openLoginModal()">Войти</a>
            </div>
        </div>

    </div>

</div>


