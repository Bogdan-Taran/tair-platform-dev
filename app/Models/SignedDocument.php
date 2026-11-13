<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignedDocument extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'document_template_id', 'child_id', 'contract_id', 'subject_key', 'kind', 'title', 'version', 'snapshot', 'checksum', 'confirmation_method', 'ip_address', 'signed_at', 'expires_at'];

    protected function casts(): array
    {
        return ['snapshot' => 'encrypted:array', 'signed_at' => 'datetime', 'expires_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function statusLabel(): string
    {
        return $this->revoked_at ? ($this->kind === 'consent' ? 'Отозвано' : 'Прекращён') : ($this->expires_at?->isPast() ? 'Истёк' : 'Действует');
    }
}
