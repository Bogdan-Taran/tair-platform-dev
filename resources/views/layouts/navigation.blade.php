<header class="main-header">
    <div class="container header-container">
        <div class="logo" onclick="window.location.href='{{ route('index') }}'">
            <img src="{{ url('frontend/src/assets/tair_logo.png') }}" alt="Логотип ТАИР">
            <span class="logo-text">ТАИР</span>
        </div>

        <nav class="main-nav" id="main-navigation">
            <ul>
                <li><a href="{{ route('our-team') }}">НАША КОМАНДА</a></li>
                <li><a href="{{ route('our-branches') }}">ФИЛИАЛЫ</a></li>
                <li><a href="{{ route('index') }}#contacts">КОНТАКТЫ</a></li>
                <li><a href="{{ route('about-club') }}">О КЛУБЕ</a></li>


                @guest()
                    <li><a href="{{ route('login') }}" data-auth-open="login"><img class="profile-icon"
                                                                src="{{ url('frontend/src/assets/icons/profile-icon.svg')}}"
                                                                alt="иконка профиля"></a></li>
                @endguest

                @auth
                    <li><a href="{{ route('dashboard') }}"><img class="profile-icon"
                                                            src="{{ url('frontend/src/assets/icons/profile-icon.svg')}}"
                                                            alt="иконка профиля"></a>
                    </li>
                    <li>
                        <form id="logoutForm" action="{{route('logout')}}" method="post" style="display: none">
                            @csrf
                        </form>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                            <img class="profile-icon" src="{{ url('frontend/src/assets/icons/logout-icon.svg')}}" alt="иконка выхода"></a>
                    </li>
                @endauth



            </ul>
        </nav>
        <button type="button" class="menu-toggle" aria-controls="main-navigation" aria-expanded="false" aria-label="Открыть меню">
            <span class="hamburger"></span>
            <span class="hamburger"></span>
            <span class="hamburger"></span>
            <span class="hamburger"></span>
        </button>

    </div>
</header>
