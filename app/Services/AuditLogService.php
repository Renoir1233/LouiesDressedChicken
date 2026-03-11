<?php
// app/Services/AuditLogService.php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogService
{
    public static function log($action, Model $model = null, $description = null, array $oldValues = null, array $newValues = null)
    {
        $logData = [
            'action' => $action,
            'description' => $description,
            'user_id' => auth()->id(),
        ];

        if ($model) {
            $logData['model_type'] = get_class($model);
            $logData['model_id'] = $model->id;
        }

        if ($oldValues) {
            $logData['old_values'] = $oldValues;
        }

        if ($newValues) {
            $logData['new_values'] = $newValues;
        }

        if (request()) {
            $logData['ip_address'] = request()->ip();
            $logData['user_agent'] = request()->userAgent();
            $logData['url'] = request()->fullUrl();
            $logData['method'] = request()->method();
        }

        return AuditLog::create($logData);
    }

    public static function logOrderCreated($order)
    {
        return self::log(
            'created',
            $order,
            "Order {$order->order_number} created by " . auth()->user()->name,
            null,
            $order->toArray()
        );
    }

    public static function logInventoryUpdate($inventory, $oldValues, $newValues)
    {
        $changes = array_diff_assoc($newValues, $oldValues);
        
        return self::log(
            'updated',
            $inventory,
            "Inventory item {$inventory->product_name} updated by " . auth()->user()->name,
            $oldValues,
            $newValues
        );
    }
}