<footer class="footer-section">
    <div class="footer-content">
        <div class="footer-lists">
            <h3>Главная</h3>
            <a href="{{ route('index') }}#about-club">О клубе</a>
            <a href="{{ route('index') }}#directions">Наши направления</a>
            <a href="{{ route('index') }}#branch-container">Наши филиалы</a>
            <a href="{{ route('index') }}#competitions">Соревнования в которых мы участвовали</a>
            <a href="{{ route('index') }}#why-us">Почему выбирают нас</a>
            <a href="{{ route('index') }}#summer-camp">Летний лагерь и сдача на берет</a>
            <a href="{{ route('index') }}#reviews">Отзывы</a>
            <a href="{{ route('index') }}#faq-section">Часто задаваемые вопросы</a>
            <a href="{{ route('index') }}#contacts">Контакты</a>
            <a href="{{ route('index') }}#see-more-section">Смотреть больше</a>
        </div>

        <hr class="vertical">

        <div class="footer-lists">
            <h3>Наш клуб</h3>
            <a href="{{ route('about-club') }}">О клубе ТАИР</a>
            <a href="{{ route('our-team') }}">Тренерский состав</a>
            <a href="{{ route('index') }}#contacts">Контакты</a>
            <a href="{{ route('index') }}#contacts-map">Как к нам добраться</a>
            <a href="{{ route('index') }}#contacts">Записаться на тренировку</a>
        </div>

        <hr class="vertical">

        <div class="footer-lists">
            <h3>Наши соцсети</h3>
            <div class="social-media">
                <div class="telegram social-media-card" onclick="window.open('https://t.me/tair_70?ysclid=mt8ehbt0jw553338366', '_blank');"><img src="{{ url('frontend/src/assets/telegram-icon-black.png')}}" alt="Телеграм иконка">
                </div>
                <div class="vk social-media-card" onclick="window.open('https://vk.ru/tairtomsk', '_blank');"><img src="{{ url('frontend/src/assets/VK-icon-black.png')}}" alt="ВК иконка">
                </div>
                <div class="instagram social-media-card" onclick="window.open('https://vk.ru/away.php?to=https%3A%2F%2Fwww.instagram.com%2Ftair_tmk%3Fr%3Dnametag&utf=1', '_blank');"><img src="{{ url('frontend/src/assets/Instagram-icon-black.png')}}"
                                                              alt="Инстаграм иконка"></div>
                <div class="max social-media-card" onclick="window.open('https://web.max.ru/263696113', '_blank');"><img src="{{ url('frontend/src/assets/icons/max-icon.svg')}}" alt="мессенджер макс иконка">
                </div>
            </div>
        </div>

    </div>
</footer>
