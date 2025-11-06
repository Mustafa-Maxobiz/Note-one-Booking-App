<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model.
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Get formatted old values.
     */
    public function getFormattedOldValuesAttribute()
    {
        return $this->old_values ? json_encode($this->old_values, JSON_PRETTY_PRINT) : null;
    }

    /**
     * Get formatted new values.
     */
    public function getFormattedNewValuesAttribute()
    {
        return $this->new_values ? json_encode($this->new_values, JSON_PRETTY_PRINT) : null;
    }
}
