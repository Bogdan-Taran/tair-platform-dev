<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'section' => ['nullable', Rule::in(['contracts', 'parents', 'children'])],
            'branch' => ['nullable', 'integer', 'exists:branches,id'],
            'q' => ['nullable', 'string', 'max:150'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'name_asc', 'name_desc'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'section.in' => 'Выберите существующий раздел.',
            'branch.exists' => 'Выберите существующий филиал.',
            'branch.integer' => 'Выберите существующий филиал.',
            'q.string' => 'Введите ФИО текстом.',
            'q.max' => 'Поиск: не более 150 символов.',
            'sort.in' => 'Выберите доступный порядок сортировки.',
            'page.integer' => 'Номер страницы должен быть целым числом.',
            'page.min' => 'Номер страницы должен быть больше нуля.',
        ];
    }
}
