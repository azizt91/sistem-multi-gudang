<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Create a new audit log entry.
     *
     * @param string $action
     * @param string $description
     * @param mixed|null $subject
     * @return AuditLog
     */
    public function log(string $action, string $description, $subject = null)
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'entity_type' => $subject ? get_class($subject) : null,
            'entity_id' => $subject ? $subject->id : null,
        ]);
    }
}
