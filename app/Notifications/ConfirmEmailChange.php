<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmEmailChange extends Notification
{
    public function __construct(public string $url) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Подтверждение новой почты — ТАИР')->greeting('Подтвердите почту')->line('Вы запросили изменение почты в профиле ТАИР. Ссылка действует 60 минут.')->action('Подтвердить почту', $this->url)->line('Если вы не запрашивали изменение, проигнорируйте письмо.');
    }
}
