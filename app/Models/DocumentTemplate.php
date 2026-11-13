<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['kind', 'version', 'title', 'body', 'valid_days', 'validity_mode'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}
