<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an action to the audit logs.
     *
     * @param string $action The action performed (e.g., 'create_transaction')
     * @param string $description A human-readable description
     * @param object|null $entity The entity being modified (optional)
     * @return AuditLog|null
     */
    public function log(string $action, string $description, $entity = null)
    {
        // Audit logs require an authenticated user
        if (!Auth::check()) {
            return null;
        }

        $entityType = null;
        $entityId = null;

        if ($entity) {
            $entityType = get_class($entity);
            $entityId = $entity->id;
        }

        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
        ]);
    }
}
