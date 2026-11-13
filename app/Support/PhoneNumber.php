<?php

namespace App\Support;

use App\Models\User;

class PhoneNumber
{
    public static function normalize(?string $value): string
    {
        $digits = preg_replace('/\D/', '', $value ?? '');
        if (strlen($digits) === 11 && $digits[0] === '8') {
            $digits = '7'.substr($digits, 1);
        }

        return $digits === '' ? '' : '+'.$digits;
    }

    public static function uniqueRule(?int $except = null): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($except): void {
            if (! is_string($value)) {
                return;
            }
            foreach (User::query()->when($except, fn ($q) => $q->whereKeyNot($except))->select('phone')->cursor() as $user) {
                if (self::normalize($user->phone) === self::normalize($value)) {
                    $fail('Этот номер телефона уже зарегистрирован.');
                    break;
                }
            }
        };
    }
}
