@extends('layouts.app')

@section('main-content')

    <div class="sidenav">
        <a href="#">Профиль</a>
        <a href="#">Документы</a>
    </div>

    <main class="main">

        <div class="all-blocks">
            <div class="three-blocks">
                <div class="profile-blocks-info">
                    <div class="profile-info-block">
                        <img src="{{ url('frontend/src/assets/edit-icon.svg')}}" alt="Изменить иконка"
                             class="edit-icon">
                        <div class="profile-photo"><img src="{{ url('frontend/src/img/about-club-image.png')}}"
                                                        alt="Боец ТАИР в клетке на ММА"></div>
                        <div class="profile-info-details">
                            <h3 class="block-header">Ченкова Эльвира Фёдоровна</h3>
                            <div class="property-info-block">
                                <p class="property-text">Дата регистрации</p>
                                <p class="info-text">17 сентрября 2026г</p>
                            </div>
                            <div class="property-info-block">
                                <p class="property-text">Дата рождения</p>
                                <p class="info-text">24 ноября 1989г</p>
                            </div>
                            <div class="property-info-block">
                                <p class="property-text">E-mail</p>
                                <p class="info-text">examplemail@mail.ru</p>
                            </div>
                            <div class="property-info-block">
                                <p class="property-text">Телефон</p>
                                <p class="info-text">88005553535</p>
                            </div>
                        </div>
                    </div>
                    <div class="children-info">
                        <h3 class="block-header">Дети</h3>
                        <div class="child-item">
                            <h4 class="child-fio">Рамазидов Илья Тимурович</h4>
                            <div class="property-info-block">
                                <p class="property-text">Дата рождения</p>
                                <p class="info-text">10 марта 2010г.</p>
                            </div>
                        </div>
                        <a class="add-child-btn">Добавить ребёнка</a>

                    </div>
                </div>
                <div class="my-branch-info">
                    <h3 class="block-header">Мой филиал</h3>
                    <div class="branch-info-section">
                        <div class="flex-col gap-4">
                            <div class="property-info-block">
                                <img src="{{ url('frontend/src/assets/edit-icon.svg')}}" alt="Изменить иконка"
                                     class="edit-icon">
                                <p>Академгородок, Пр. Академический, 17</p>
                            </div>
                            <div class="property-info-block">
                                <img src="{{ url('frontend/src/assets/edit-icon.svg')}}" alt="Изменить иконка"
                                     class="edit-icon">
                                <div class="flex-col">
                                    <p>Ярослав Таран</p>
                                    <p>Максим Кашников</p>
                                </div>
                            </div>
                            <div class="branch-phone">
                                <img src="{{ url('frontend/src/assets/edit-icon.svg')}}" alt="Изменить иконка"
                                     class="edit-icon">
                                <p>8-952-889-45-15</p>
                            </div>
                        </div>
                        <div class="branch-photo"><img
                                src="{{ url('frontend/src/img/halls-adventure/photo-academ2.png')}}" alt="Тренировка"
                                class="photo-img"></div>
                    </div>
                    <h4 class="block text-start">Направления:</h4>
                    <div class="branch-directions">
                        <p>ММА</p>
                        <p>СМЕШАННЫЕ ЕДИНОБОРТСВА</p>
                        <p>БОРЬБА</p>
                        <p>ЕДИНОБОРСТВА</p>
                        <p>ОФП</p>
                        <p>РУКОПАШНЫЙ БОЙ</p>
                        <p>ПАНКРАТИОН</p>
                        <p>АБСОЛЮТНЫЙ РЕАЛЬНЫЙ БОЙ</p>
                        <p>СРЕДСТВА ЗАЩИТЫ И САМООБОРОНЫ</p>
                        <p>АРБ</p>
                    </div>
                    <h4 class="block text-center">Проложить маршрут</h4>

                </div>
            </div>
            <div class="schedule-block">
                <h3 class="block-header">
                    Расписание
                </h3>
            </div>
        </div>

    </main>

@endsection


@push('styles')
    <link rel="stylesheet" href="{{ url('frontend/css/style-dashboard.css')}}">
@endpush
