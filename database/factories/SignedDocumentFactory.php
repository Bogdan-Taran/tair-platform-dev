<?php

namespace Database\Factories;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SignedDocumentFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'document_template_id' => DocumentTemplate::factory(), 'subject_key' => 'self', 'kind' => 'contract', 'title' => 'Тестовый договор', 'version' => 'test', 'snapshot' => ['body' => 'Тест'], 'checksum' => hash('sha256', 'test'), 'confirmation_method' => 'password_and_checkbox', 'ip_address' => '127.0.0.1', 'signed_at' => now(), 'expires_at' => now()->addYear()];
    }
}
