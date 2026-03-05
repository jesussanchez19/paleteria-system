<?php

use App\Models\AuditLog;

if (! function_exists('audit_log')) {
    function audit_log(string $action, string $module, $entity = null, array $meta = []): void
    {
        $entityType = null;
        $entityId = null;
        $entityName = null;

        if ($entity) {
            $entityType = class_basename($entity);
            $entityId = $entity->id ?? null;
            
            // Detectar nombre de la entidad
            $entityName = $entity->name 
                ?? $entity->title 
                ?? $entity->description 
                ?? null;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => array_merge(['_entity_name' => $entityName], $meta ?: []),
        ]);
    }
}
