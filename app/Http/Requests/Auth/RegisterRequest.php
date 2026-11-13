<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => PhoneNumber::normalize($this->input('phone'))]);
        }
        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'patronymic' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+[0-9]{10,15}$/', 'unique:'.User::class, PhoneNumber::uniqueRule()],
            'birthdate' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'account_type' => ['sometimes', Rule::in(['parent', 'athlete'])],
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
            'birthdate.before_or_equal' => 'Регистрация доступна с 18 лет.',
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
