<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTemplateFactory extends Factory
{
    public function definition(): array
    {
        return ['kind' => 'contract', 'title' => 'Тестовый договор', 'version' => fake()->unique()->uuid(), 'body' => 'Текст для автоматического испытания.', 'valid_days' => 365, 'published_at' => now()];
    }
}
