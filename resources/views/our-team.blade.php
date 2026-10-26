@extends('layouts.app')

@section('title')
    Наша команда
@endsection

@section('main-content')
    <main id="main-container">

        <h1>Наша команда</h1>

        <section>
            <article class="container section-trainer-yaroslav">
                <div class="trainer-content">
                    <div class="trainer-image-container trainer-yaroslav-image-container">
                        <img src="{{ url('frontend/src/img/coaching-staff/yaroslav-coach.png')}}"
                             alt="Тренер Ярослав Таран">
                    </div>

                    <div class="trainer-text-container">
                        <h2>Ярослав Таран</h2>
                        <p>Стать тренером – это не просто выбор профессии, это выбор жизненного пути, который требует
                            самоотдачи, терпения и любви к своему делу. И я благодарен судьбе за возможность быть частью
                            этого удивительного мира спорта и помогать другим людям становиться лучше, сильнее,
                            увереннее. Я знаю, что впереди ещё много работы и новых вызовов, но я готов к ним, потому
                            что верю в своё дело и в тех, кого я тренирую.</p>
                        <span class="team-role">Главный тренер по единоборствам</span>
                    </div>
                </div>
            </article>

            <article class="container section-trainer-quote">
                <blockquote class="trainer-quote trainer-quote-yaroslav">
                    <p>“Я не могу сделать их чемпионами, но могу помочь им прийти к этому, раскрыть их возможности и
                        осуществить мечты.”</p>
                    <cite>-Ярослав Таран</cite>
                </blockquote>
            </article>


            <article class="container section-trainer-irina">
                <div class="trainer-content">
                    <div class="trainer-text-container trainer-text-container-irina">
                        <h2>Ирина Некрасова</h2>
                        <p>Я просто знала, что если это можно сделать, то это нужно сделать, и я это сделала. Кто-то
                            хочет, чтобы это произошло, кто-то желает, чтобы это произошло, а кто-то делает это
                            возможным</p>
                        <span class="team-role">Администратор, тренер по ОФП, ЛФК, АФК</span>
                    </div>
                    <div class="trainer-image-container trainer-irina-image-container">
                        <img src="{{ url('frontend/src/img/coaching-staff/irina-coach.png')}}"
                             alt="Тренер Ирина Некрасова">
                    </div>
                </div>
            </article>

            <article class="container section-trainer-quote">
                <blockquote class="trainer-quote trainer-quote-irina">
                    <p>“Ты поймёшь когда начнёшь”</p>
                    <cite>-Ирина Некрасова</cite>
                </blockquote>
            </article>

            <!-- Елена Васильевна -->
            <article class="container section-trainer-yaroslav section-trainer-elena">
                <div class="trainer-content">
                    <div class="trainer-image-container trainer-yaroslav-image-container trainer-elena-image-container">
                        <img src="{{ url('frontend/src/img/coaching-staff/elena_kovaleva.png')}}"
                             alt="Тренер Елена Ковалёва">
                    </div>

                    <div class="trainer-text-container">
                        <h2>Елена Ковалёва</h2>
                        <p>Красивая осанка, открытость миру, решительность иди дальше – вот здоровый ребенок! Я стараюсь
                            своим личным примером показать родителям, детям, бабушкам, дедушкам, что движение,
                            физкультура и спорт должны жить в сердце каждого!</p>
                        <span class="team-role">Тренер-помощник по ОФП, ЛФК и АФК</span>
                    </div>
                </div>
            </article>

            <article class="container section-trainer-quote">
                <blockquote class="trainer-quote  trainer-quote-elena">
                    <p>"В здоровом теле здоровый дух"</p>
                    <cite>-Елена Ковалёва</cite>
                </blockquote>
            </article>
        </section>


        <!--    Секция тренеры-инструкторы-->
        <hr class="horizontal-instructors ">

        <section class="section-trainers-instructors container">
            <header>
                <div class="trainers-instructors-left">
                    <img src="{{ url('frontend/src/img/coaching-staff/trainers-instructors-maksim-savely.jpg')}}"
                         alt="Тренеры-инструкторы Савелий и Максим вдвоём">
                </div>
                <div class="trainers-instructors-right">
                    <h2>Тренеры-инструкторы</h2>
                    <p>Мы воспитываем всех бойцов в равных условиях, но некоторые из них начинают себя активно
                        проявлять. Мы
                        это видим и даём возможность развития в сфере</p>
                    <div class="trainers-instructors-bottom-img">
                        <img
                            src="{{ url('frontend/src/img/coaching-staff/trainers-instructors-maksim-savely-yaroslav.jpg')}}"
                            alt="Тренеры-инструкторы Савелий и Максим на соревнованиях с Ярославом">
                    </div>
                </div>
            </header>

            <article class="article-trainers-instructors article-trainers-instructors-maksim">
                <header class="header-text-trainers-instructors header-text-trainers-instructors-maxim">
                    <div class="image-trainer-instructor">
                        <img src="{{ url('frontend/src/img/coaching-staff/maxim-trainer-instructor.png')}}"
                             alt="Фото тренера-инструктора Максима" class="image-trainer">
                    </div>
                    <div class="trainers-instructors-info">
                        <h3>Максим Кашников</h3>
                        <p>С детства я занимался разными видами спорта — от лыжных гонок до тайского бокса. Найдя своё
                            направление в клубе «Таир», благодаря самоотдаче и дисциплине прошёл путь от новичка до
                            инструктора. Сегодня веду тренировки в одном из филиалов и помогаю другим делать первые
                            уверенные шаги в спорте.</p>
                        <span class="team-role">Тренер-инструктор по единоборствам</span>
                    </div>
                </header>
                <blockquote class="trainer-quote trainer-quote-maxim">
                    <p>Неважно, откуда ты пришел и какой у тебя бэкграунд.
                        Важно, готов ли ты выкладываться прямо сейчас</p>
                    <cite>-Максим Кашников</cite>
                </blockquote>
            </article>

        </section>


        <section class="team-behind-scenes" aria-label="Команда за кулисами">
            <div class="team-behind-scenes-intro">
                <p>А также тёмные лошадки нашего клуба, которых не видно,<br>
                    но они работают из-за кулис</p>
            </div>

            <div class="container team-support">
                <article class="team-support-member" aria-labelledby="alexandra-name">
                    <div class="team-support-card">
                        <div class="team-support-portrait">
                            <img src="{{ asset('frontend/src/img/coaching-staff/alexandra.png') }}"
                                 alt="Александра — администратор и секретарь клуба" width="433" height="650"
                                 loading="lazy">
                        </div>
                        <div class="team-support-info">
                            <h2 id="alexandra-name">Александра</h2>
                            <p>Ответственность — основа, на которой строится успех всей команды и клуба</p>
                            <span class="team-role">Администратор и секретарь клуба</span>
                        </div>
                    </div>
                    <blockquote class="trainer-quote trainer-quote-maxim team-support-quote">
                        <p>Неважно, откуда ты пришел и какой у тебя бэкграунд.<br>
                            Важно, готов ли ты выкладываться прямо сейчас</p>
                        <cite>-Максим Кашников</cite>
                    </blockquote>
                </article>

                <article class="team-support-member" aria-labelledby="bogdan-name">
                    <div class="team-support-card team-support-card-reverse">
                        <div class="team-support-portrait">
                            <img src="{{ asset('frontend/src/img/coaching-staff/bogdan-taran.png') }}"
                                 alt="Богдан Таран — менеджер-техник" width="421" height="631" loading="lazy">
                        </div>
                        <div class="team-support-info">
                            <h2 id="bogdan-name">Богдан Таран</h2>
                            <p>Занимаюсь электронной и технической составляющей клуба, пиар и медиа-сопровождением</p>
                            <span class="team-role">Менеджер-техник</span>
                        </div>
                    </div>
                    <blockquote class="trainer-quote trainer-quote-irina team-support-quote">
                        <p>“Увлечён и работаю по новому”</p>
                        <cite>-Богдан Таран</cite>
                    </blockquote>
                </article>
            </div>
        </section>

    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/style-coaching-staff.css')}}">
@endpush
