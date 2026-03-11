<?php
// app/Http/Middleware/AuditLogMiddleware.php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        // Don't log login requests (they're handled in AuthController)
        if ($request->routeIs('login')) {
            return;
        }

        // Don't log AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return;
        }

        // Don't log if user is not authenticated
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();
        $method = $request->method();
        $url = $request->fullUrl();
        
        // Skip logging for certain methods/urls
        $skipMethods = ['GET']; // You can add methods to skip
        $skipUrls = ['/audit-logs', '/api/']; // URLs to skip
        
        foreach ($skipUrls as $skipUrl) {
            if (strpos($url, $skipUrl) !== false) {
                return;
            }
        }

        if (in_array($method, $skipMethods)) {
            return;
        }

        // Determine action based on method
        $action = $this->getActionFromMethod($method);
        
        // Create audit log
        AuditLog::create([
            'action' => $action,
            'description' => "{$action} action performed on {$url}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $url,
            'method' => $method,
            'user_id' => $user->id,
        ]);
    }

    private function getActionFromMethod($method)
    {
        $actions = [
            'POST' => 'created',
            'PUT' => 'updated',
            'PATCH' => 'updated',
            'DELETE' => 'deleted',
            'GET' => 'viewed',
        ];

        return $actions[$method] ?? 'accessed';
    }
}