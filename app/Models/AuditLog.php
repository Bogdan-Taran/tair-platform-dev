<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = ['actor_id', 'action', 'subject_type', 'subject_id', 'details'];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }

    public static function record(string $action, string $type, ?int $id = null, array $details = []): void
    {
        self::create(['actor_id' => auth()->id(), 'action' => $action, 'subject_type' => $type, 'subject_id' => $id, 'details' => $details]);
    }
}
