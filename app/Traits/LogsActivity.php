<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected array $logSkipAttributes = [];

    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity('created', 'Baru dibuat', $model->getAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }
            $before = array_intersect_key($model->getOriginal(), $dirty);
            $model->logActivity('updated', 'Diubah', [
                'before' => $before,
                'after' => $dirty,
            ]);
        });

        static::deleted(function ($model) {
            $isForce = method_exists($model, 'isForceDeleting') && $model->isForceDeleting();
            if ($isForce) {
                return;
            }
            $model->logActivity('deleted', 'Dibatalkan / dihapus (soft)', $model->getOriginal());
        });
    }

    protected function logActivity(string $event, string $description, ?array $properties = null): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'description' => $description,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'properties' => $properties,
        ]);
    }
}
