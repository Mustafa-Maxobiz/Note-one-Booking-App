<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the auditable trait.
     */
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            static::logAuditEvent($model, 'created');
        });

        static::updated(function (Model $model) {
            static::logAuditEvent($model, 'updated');
        });

        static::deleted(function (Model $model) {
            static::logAuditEvent($model, 'deleted');
        });

        static::restored(function (Model $model) {
            static::logAuditEvent($model, 'restored');
        });
    }

    /**
     * Log audit event.
     */
    protected static function logAuditEvent(Model $model, string $event)
    {
        $oldValues = null;
        $newValues = null;

        if ($event === 'updated') {
            $oldValues = $model->getOriginal();
            $newValues = $model->getChanges();
        } elseif ($event === 'created') {
            $newValues = $model->getAttributes();
        } elseif ($event === 'deleted') {
            $oldValues = $model->getAttributes();
        } elseif ($event === 'restored') {
            $newValues = $model->getAttributes();
        }

        AuditLog::create([
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
        ]);
    }

    /**
     * Get audit logs for this model.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
