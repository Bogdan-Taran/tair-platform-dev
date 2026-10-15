@extends('layouts.app')

@section('main-content')

    <div class="sidenav">
        <a href="#">Профиль</a>
        <a href="#">Документы</a>
    </div>

    <main class="main">

        <div class="three-blocks">
            <div class="profile-blocks-info">
                <div class="profile-info-block">
                    <img src="{{ url('frontend/src/assets/edit-icon.svg')}}" alt="Изменить иконка"
                         class="edit-icon">
                    <div class="profile-photo"><img src="{{ url('frontend/src/img/about-club-image.png')}}" alt="Боец ТАИР в клетке на ММА"></div>
                    <div class="profile-info-details">
                        <h3 class="fio">Ченкова Эльвира Фёдоровна</h3>
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
                <div class="children-info"></div>
            </div>
            <div class="my-branch-info"></div>
        </div>
        <div class="schedule-block"></div>

    </main>

@endsection


@push('styles')
    <link rel="stylesheet" href="{{ url('frontend/css/style-dashboard.css')}}">
@endpush
