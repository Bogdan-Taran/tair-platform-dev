@extends('layouts.app')

@section('main-content')
    <main class="main-sign-contract">
        <h2>Договор-оферта и Правила оказания услуг<br>в спортивном клубе «ТАИР»</h2>
        <p>Пожалуйста, ознакомьтесь со следующей важной информацией перед продолжением.</p>
        <p>С полным текстом документа вы можете ознакомиться по ссылке ниже.</p>
        <a href="#">Договор Единоборства 2026-2027</a>
        <p class="paragraph-part-from-contract">
            Услуги предоставляются в соответствии с утвержденным регламентом спортивного клуба. Оплачивая и активируя
            абонемент, вы подтверждаете, что делаете это добровольно, осознаете риски и берете на себя ответственность
            за состояние своего здоровья во время тренировок.
            Некоторые виды физической активности могут иметь медицинские противопоказания. Настоятельно рекомендуем
            пройти предварительный осмотр у врача перед началом занятий. Клуб не несет ответственности за травмы,
            полученные в результате нарушения правил техники безопасности или сокрытия медицинских противопоказаний.
            Если вы не уверены в уровне своей физической подготовки или имеете хронические заболевания, пожалуйста,
            проконсультируйтесь с тренером клуба для подбора индивидуальной программы до начала самостоятельных занятий.
        </p>

        <h3>
            Стороны
        </h3>

        <div class="contract-sides-block">
            <div class="side-block">
                <h4>Исполнитель:</h4>
                <p>Таран Ярослав Дмитриевич,<br>далее именуемый «Тренер», с одной стороны</p>

            </div>

            <div class="side-block">
                <h4>Заказчик:</h4>
                <p>Ченкова Эльвира Фёдоровна,<br>далее именуемый «Заказчик», с другой
                    стороны, действующий в интересах<br>Рамазидов Илья Тимурович, далее именуемый «Ученик»</p>
            </div>
        </div>

        <p class="text-center">Вместе именуемые Стороны, заключили настоящий договор.</p>
        <h3>
            Реквизиты сторон
        </h3>

        <div class="contract-sides-block">
            <div class="side-block">
                <p>Российская Федерация КСЕ «ТАИР» г.Томск<br>
                    ИНН 701714476685<br>
                    Бик 046902606 ПАО «Сбербанк» г.Томск,<br>
                    Код подразделения банка 44/8616/00158<br>
                    Лицевой счет 40817810064005000044</p>
            </div>

            <div class="side-block">

            </div>
        </div>


        <form action="#" id="agreementForm">
            @csrf
            <div class="agreement-block">
                <label class="radio-label">
                    <input type="radio" name="agreement" value="accept" id="acceptRadio">
                    <span>Я принимаю условия договора и правила клуба</span>
                </label>

                <label class="radio-label">
                    <input type="radio" name="agreement" value="decline" id="declineRadio" checked>
                    <span>Я не принимаю условия договора</span>
                </label>
            </div>
            <div class="form-buttons">
                <a href="{{ route('dashboard') }}" class="btn-cancel">Отмена</a>
                <button onclick="" id="nextButton" class="btn-next" disabled>Далее</button>
            </div>
        </form>

    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('frontend/css/style-sign-contract.css')}}">
@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const acceptRadio = document.getElementById('acceptRadio');
            const declineRadio = document.getElementById('declineRadio');
            const nextButton = document.getElementById('nextButton');

            function toggleButton() {
                if (acceptRadio.checked) {
                    nextButton.removeAttribute('disabled');
                } else {
                    nextButton.setAttribute('disabled', 'true');
                }
            }

            acceptRadio.addEventListener('change', toggleButton);
            declineRadio.addEventListener('change', toggleButton);
        });
    </script>
@endpush
