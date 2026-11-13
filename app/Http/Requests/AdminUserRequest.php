<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
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
            'firstname' => ['required', 'string', 'max:255'], 'lastname' => ['required', 'string', 'max:255'], 'patronymic' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->route('user')?->id)],
            'phone' => ['required', 'regex:/^\+[0-9]{10,15}$/', PhoneNumber::uniqueRule($this->route('user')?->id)],
            'birthdate' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'account_type' => ['required', Rule::in(['parent', 'athlete'])],
            'role' => ['required', Rule::in(['user', 'admin'])],
        ];
    }
}
