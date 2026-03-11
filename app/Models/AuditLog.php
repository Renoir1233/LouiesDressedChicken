<?php
// app/Models/AuditLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'user_id'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    // Helper Methods
    public function getActionColorAttribute()
    {
        $colors = [
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'viewed' => 'info',
            'login' => 'primary',
            'logout' => 'secondary',
            'exported' => 'dark',
        ];

        return $colors[strtolower($this->action)] ?? 'secondary';
    }

    public function getModelNameAttribute()
    {
        if (!$this->model_type) {
            return 'System';
        }

        $model = class_basename($this->model_type);
        return str_replace('_', ' ', strtolower($model));
    }

    public function getChangesSummaryAttribute()
    {
        if (!$this->old_values && !$this->new_values) {
            return 'No changes';
        }

        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];
        
        $changes = [];
        foreach ($new as $key => $value) {
            if (!isset($old[$key]) || $old[$key] != $value) {
                $changes[] = $key;
            }
        }

        if (count($changes) > 3) {
            return implode(', ', array_slice($changes, 0, 3)) . ' and ' . (count($changes) - 3) . ' more';
        }

        return implode(', ', $changes);
    }
}