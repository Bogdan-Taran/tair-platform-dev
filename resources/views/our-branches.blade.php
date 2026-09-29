@extends('layouts.app')

@section('title')
    Наши филиалы
@endsection


@section('main-content')

    <main>
        <section class="container halls-adventure-section">

            <header class="header">
                <h1>Экскурсия по залам</h1>
                <p>Приглашаем вас на виртуальную экскурсию по залам. Посмотрите, как мы<br>заботимся о вашем коморте
                    и качестве тренировок</p>
            </header>

            <hr class="hor-line">

            <article class="container-content">
                <div class="info">
                    <h2>Академгородок</h2>
                    <p>Высокооборудованный под единоборства и ОФП спортивный зал. Здесь основное место, где проходят
                        тренировки по всем направлениям. Зал оборудован мягким покрытием “татами”, есть мягкие
                        спортивные
                        маты, на которые бойцы отрабатывают броски и боксёрские груши. Также есть хореографические
                        станки,
                        турники, шведская стенка, мягкие модули, гантели и всё, что может быть задействовано в
                        физическом
                        развитии</p>
                    <button class="button-base cta-button sign-in-button1"
                            onclick="window.location.href='tel:+79138005368'">
                        ЗАПИСАТЬСЯ
                    </button>
                </div>

                <div class="photo academ-content-photo slick-wrapper">
                    <img src="{{ url('frontend/src/img/halls-adventure/photo-academ1.png')}}" alt="Боксёрский зал" class="photo-img">
                    <div class="pair-images">
                        <img src="{{ url('frontend/src/img/halls-adventure/photo-academ2.png')}}" alt="Тренировка" class="photo-img">
                        <img src="{{ url('frontend/src/img/halls-adventure/photo-academ3.png')}}" alt="Групповая тренировка"
                             class="photo-img">
                    </div>
                </div>
                <button class="button-base cta-button sign-in-button2"
                        onclick="window.location.href='tel:+79138005368'">
                    ЗАПИСАТЬСЯ
                </button>
            </article>


            <article class="container-content container-content-53school">
                <button class="button-base cta-button sign-in-button2"
                        onclick="window.location.href='tel:+79138005368'">
                    ЗАПИСАТЬСЯ
                </button>
                <div class="photo school53-content-photo">
                    <div class="pair-images">
                        <img src="{{ url('frontend/src/img/halls-adventure/hall-school53_1.jpg')}}" alt="Ребята все вместе в зале"
                             class="photo-img">
                        <img src="{{ url('frontend/src/img/halls-adventure/hall-school53_2.jpg')}}" alt="Вид на зал в школе 53"
                             class="photo-img">
                    </div>
                    <img src="{{ url('frontend/src/img/halls-adventure/hall-school53_3.jpg')}}" alt="Дети спарингуются в зале школы 53"
                         class="photo-img">
                </div>


                <div class="info info-53school">
                    <h2>Школа № 53 (Бела-Куна, 1)</h2>
                    <p>Малый спортивный зал в школе, в котором проходят тренировки по единоборствам у клуба ТАИР. Зал
                        оборудован мягким напольным покрытием “татами”, есть шведские стенки и турники. Тренировки
                        проходят
                        комфортно и как всегда продуктивно</p>
                    <button class="button-base cta-button sign-in-button1"
                            onclick="window.location.href='tel:+79138005368'">
                        ЗАПИСАТЬСЯ
                    </button>
                </div>

            </article>

        </section>
    </main>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('frontend/css/style-halls-adventure.css')}}">

    <!--    стили slick-->
{{--    <link rel="stylesheet" type="text/css" href="{{ url('frontend/slick/slick.css')}}"/>--}}
{{--    <link rel="stylesheet" type="text/css" href="{{ url('frontend/slick/slick-theme.css')}}"/>--}}
@endpush

@push('scripts')
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
{{--    <script type="text/javascript" src="../slick/slick.min.js"></script>--}}
{{--    <script type="text/javascript" src="../js/slider-filials.js"></script>--}}
@endpush

