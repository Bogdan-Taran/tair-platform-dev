<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'max:255'], 'lastname' => ['required', 'string', 'max:255'],
            'patronymic' => ['nullable', 'string', 'max:255'], 'birthdate' => ['required', 'date', 'before:today'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ];
    }

    public function messages(): array
    {
        return ['required' => 'Заполните поле :attribute.', 'branch_id.exists' => 'Выберите существующий филиал.', 'birthdate.before' => 'Дата рождения должна быть в прошлом.'];
    }
}
