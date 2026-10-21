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

        <form action="{{route('sign-contract')}}" method="post">
            @csrf
            <div class="contract-sides-block">
                <div class="side-block">
                    <p>Российская Федерация КСЕ «ТАИР» г.Томск<br>
                        ИНН 701714476685<br>
                        Бик 046902606 ПАО «Сбербанк» г.Томск,<br>
                        Код подразделения банка 44/8616/00158<br>
                        Лицевой счет 40817810064005000044</p>
                </div>

                <div class="side-block">
                    <h4>Паспорт:</h4>
                    @if ($errors->any())
                        <div class="alert alert-danger" style="color: #610006; font-size: 14px;" >
                            Пожалуйста, проверьте правильность заполнения паспорта.
                        </div>
                    @endif
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger" style="color: #610006; font-size: 14px;" >
                            {{ $error }}
                        </div>
                    @endforeach
                    <div class="form-row inline-row">
                        <div class="input-group">
                            <label for="series">Серия:</label>
                            <input type="text" id="series" name="series" maxlength="4"
                                   value="{{ old('series', $passport->series ?? '') }}"  autocomplete="off">
                        </div>
                        <div class="input-group">
                            <label for="number">Номер:</label>
                            <input type="text" id="number" name="number" maxlength="6"
                                   value="{{ old('number', $passport->number ?? '') }}"  autocomplete="off">
                        </div>
                    </div>
                    @error('series') <span class="alert alert-danger" style="color: #610006; font-size: 14px;">{{ $message }}</span> @enderror
                    @error('number') <span class="alert alert-danger" style="color: #610006; font-size: 14px;">{{ $message }}</span> @enderror

                    <div class="form-row full-width-row">
                        <label for="issue_by">Кем выдан:</label>
                        <input type="text" id="issue_by" name="issue_by"
                               value="{{ old('issue_by', $passport->issue_by ?? '') }}" >
                    </div>
                    @error('issue_by') <span class="alert alert-danger" style="color: #610006; font-size: 14px;">{{ $message }}</span> @enderror

                    <div class="form-row full-width-row">
                        <label for="issue_date">Когда выдан:</label>
                        <input type="date" id="issue_date" name="issue_date"
                               value="{{ old('issue_date', $passport->issue_date ?? '') }}" >
                    </div>
                    @error('issue_date') <span class="alert alert-danger" style="color: #610006; font-size: 14px;">{{ $message }}</span> @enderror
                    <div class="form-row full-width-row">
                        <label for="address">Адрес:</label>
                        <input type="text" id="address" name="address"
                               value="{{ old('address', $passport->address ?? '') }}" >
                    </div>
                    @error('address') <span class="alert alert-danger" style="color: #610006; font-size: 14px;">{{ $message }}</span> @enderror

                </div>
            </div>

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
                <button type="submit" id="acceptButton" class="btn-accept" disabled>Принять</button>
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
            const acceptButton = document.getElementById('acceptButton');

            function toggleButton() {
                if (acceptRadio.checked) {
                    acceptButton.removeAttribute('disabled');
                } else {
                    acceptButton.setAttribute('disabled', 'true');
                }
            }

            acceptRadio.addEventListener('change', toggleButton);
            declineRadio.addEventListener('change', toggleButton);
        });
    </script>
@endpush
