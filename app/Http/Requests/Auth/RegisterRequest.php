<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'patronymic' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9 ()-]{10,20}$/', 'unique:'.User::class],
            'birthdate' => ['required', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'agreement' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Заполните поле «:attribute».',
            'string' => 'Поле «:attribute» должно содержать текст.',
            'max' => 'Поле «:attribute» слишком длинное.',
            'email.email' => 'Введите корректную почту.',
            'email.unique' => 'Эта почта уже зарегистрирована.',
            'phone.unique' => 'Этот номер телефона уже зарегистрирован.',
            'phone.regex' => 'Введите корректный номер телефона.',
            'birthdate.date' => 'Введите корректную дату рождения.',
            'birthdate.before' => 'Дата рождения должна быть в прошлом.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Пароль должен содержать не менее :min символов.',
            'agreement.accepted' => 'Подтвердите согласие на обработку персональных данных.',
        ];
    }

    public function attributes(): array
    {
        return ['firstname' => 'Имя', 'lastname' => 'Фамилия', 'patronymic' => 'Отчество', 'phone' => 'Телефон', 'email' => 'Почта', 'birthdate' => 'Дата рождения', 'password' => 'Пароль'];
    }
}
