<?php
// app/Http/Controllers/AuditLogController.php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:audit-logs.*')->only(['index', 'show', 'export']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $action = $request->get('action');
        $user_id = $request->get('user_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        
        $logs = AuditLog::with('user')
            ->when($search, function($query) use ($search) {
                return $query->where('description', 'like', "%{$search}%")
                            ->orWhere('model_type', 'like', "%{$search}%")
                            ->orWhereHas('user', function($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
            })
            ->when($action, function($query) use ($action) {
                return $query->where('action', $action);
            })
            ->when($user_id, function($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            })
            ->when($date_from, function($query) use ($date_from) {
                return $query->whereDate('created_at', '>=', $date_from);
            })
            ->when($date_to, function($query) use ($date_to) {
                return $query->whereDate('created_at', '<=', $date_to);
            })
            ->latest()
            ->paginate(25);

        $users = User::whereIn('id', AuditLog::distinct()->pluck('user_id'))->get();
        $actions = AuditLog::distinct()->pluck('action');
        
        return view('audit-logs.index', compact('logs', 'users', 'actions', 'search', 'action', 'user_id', 'date_from', 'date_to'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('audit-logs.show', compact('auditLog'));
    }

    public function export(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->date_from, function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->get();

        $fileName = 'audit-logs-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Action',
                'Model Type',
                'Model ID',
                'Description',
                'User',
                'IP Address',
                'URL',
                'Method',
                'Created At'
            ]);

            // Add data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->action,
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->model_id ?? 'N/A',
                    $log->description,
                    $log->user ? $log->user->name : 'System',
                    $log->ip_address,
                    $log->url,
                    $log->method,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function dashboardStats()
    {
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'top_users' => AuditLog::select('user_id', DB::raw('count(*) as log_count'))
                ->with('user')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderByDesc('log_count')
                ->limit(5)
                ->get(),
            'recent_actions' => AuditLog::with('user')
                ->latest()
                ->limit(10)
                ->get(),
            'actions_by_type' => AuditLog::select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderByDesc('count')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}