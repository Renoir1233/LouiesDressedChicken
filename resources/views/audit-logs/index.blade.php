<!-- resources/views/audit-logs/index.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-clipboard-list me-2 text-accent"></i>Audit Logs</h4>
        <div>
            <a href="{{ route('audit-logs.export') }}?{{ http_build_query(request()->query()) }}" 
               class="btn btn-primary me-2">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <div class="modal fade" id="filterModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filter Logs</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="GET" action="{{ route('audit-logs.index') }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Search logs...">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Action</label>
                                <select name="action" class="form-control">
                                    <option value="">All Actions</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">Clear</a>
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4 justify-content-center">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                         <h2 class="mb-2">{{ number_format($logs->total()) }}</h2>
                         <p class="mb-0"><i class="fas fa-list me-1"></i>Total Logs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                         <h2 class="mb-2">{{ number_format($logs->where('action', 'created')->count()) }}</h2>
                         <p class="mb-0"><i class="fas fa-plus-circle me-1"></i>Create Actions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                         <h2 class="mb-2">{{ number_format($logs->where('action', 'updated')->count()) }}</h2>
                         <p class="mb-0"><i class="fas fa-edit me-1"></i>Update Actions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-table me-2 text-accent"></i>Audit Logs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th width="100">Action</th>
                                <th>Description</th>
                                <th width="150">User</th>
                                <th width="120">Model</th>
                                <th width="120">IP Address</th>
                                <th width="150">Date & Time</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td><code>#{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $log->action_color }}">
                                        <i class="fas fa-{{ $log->action == 'login' ? 'sign-in-alt' : ($log->action == 'logout' ? 'sign-out-alt' : 'edit') }} me-1"></i>
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="text-truncate" style="max-width: 300px;" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $log->user->avatar_url }}" alt="{{ $log->user->name }}" 
                                                 class="rounded-circle me-2" width="24" height="24">
                                            <span class="text-truncate" style="max-width: 100px;">{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->model_type)
                                        <span class="badge bg-dark">{{ $log->model_name }}</span>
                                        @if($log->model_id)
                                            <small class="text-muted d-block">#{{ $log->model_id }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small><code>{{ $log->ip_address }}</code></small>
                                </td>
                                <td>
                                    <small>{{ $log->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('audit-logs.show', $log->id) }}" 
                                       class="btn btn-edit btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 d-block text-accent"></i>
                                    No audit logs found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination - FIXED with custom sizing -->
                @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if($logs->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->previousPageUrl() }}" rel="prev" aria-label="Previous">
                                        &laquo;
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                                @if($page == $logs->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if($logs->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->nextPageUrl() }}" rel="next" aria-label="Next">
                                        &raquo;
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom styles for better card layout */
    .card-body .card {
        border: 1px solid rgba(0,0,0,.125);
        box-shadow: 0 2px 4px rgba(0,0,0,.05);
    }
    
    .stats-card {
        height: 100%;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stats-card h2 {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .table th {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 10px;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        font-size: 13px;
    }
    
    .table tbody tr:hover {
        background-color: rgba(244, 163, 0, 0.05);
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
    
    /* Ensure cards have equal height */
    .row {
        display: flex;
        flex-wrap: wrap;
    }
    
    .row > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }
    
    /* Custom pagination styling */
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination-sm .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    .pagination-sm .page-item:first-child .page-link {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    
    .pagination-sm .page-item:last-child .page-link {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    
    .page-link {
        color: var(--primary-color);
        background-color: #fff;
        border: 1px solid #dee2e6;
    }
    
    .page-link:hover {
        color: var(--secondary-color);
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .page-item.active .page-link {
        color: #fff;
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    /* Smaller arrow size */
    .pagination-sm .page-link {
        min-width: 30px;
        text-align: center;
    }
</style>
@endsection