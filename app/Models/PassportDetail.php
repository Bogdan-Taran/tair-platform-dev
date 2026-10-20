<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassportDetail extends Model
{
    protected $fillable = [
        'user_id',
        'series',
        'number',
        'issue_date',
        'issue_by',
        'address',
    ];
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
