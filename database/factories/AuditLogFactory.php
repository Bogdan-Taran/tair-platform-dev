<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return ['action' => 'user.updated', 'subject_type' => 'user', 'subject_id' => 1, 'details' => []];
    }
}
