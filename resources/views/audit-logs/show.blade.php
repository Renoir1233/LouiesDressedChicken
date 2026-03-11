<!-- resources/views/audit-logs/show.blade.php -->
@extends('layouts.employees')

@section('content')
@php
    $currentUser = auth()->user();
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-clipboard-list me-2 text-accent"></i>Audit Log Details</h4>
        <a href="{{ route('audit-logs.index') }}" class="btn btn-delete">
            <i class="fas fa-arrow-left me-2"></i>Back to Logs
        </a>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-accent"></i>Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Log ID</strong></label>
                                <p class="form-control-plaintext">
                                    <code>#{{ str_pad($auditLog->id, 6, '0', STR_PAD_LEFT) }}</code>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Action</strong></label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $auditLog->action_color }}">
                                        <i class="fas fa-{{ $auditLog->action == 'login' ? 'sign-in-alt' : ($auditLog->action == 'logout' ? 'sign-out-alt' : 'edit') }} me-1"></i>
                                        {{ ucfirst($auditLog->action) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label"><strong>Description</strong></label>
                                <p class="form-control-plaintext">{{ $auditLog->description }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>User</strong></label>
                                <p class="form-control-plaintext">
                                    @if($auditLog->user)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $auditLog->user->avatar_url }}" alt="{{ $auditLog->user->name }}" 
                                                 class="rounded-circle me-2" width="30" height="30">
                                            <span>{{ $auditLog->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Date & Time</strong></label>
                                <p class="form-control-plaintext">
                                    {{ $auditLog->created_at->format('M d, Y h:i:s A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Details -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-server me-2 text-accent"></i>Technical Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>IP Address</strong></label>
                                <p class="form-control-plaintext">
                                    <code>{{ $auditLog->ip_address ?? 'N/A' }}</code>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>HTTP Method</strong></label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-dark">{{ $auditLog->method ?? 'N/A' }}</span>
                                </p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label"><strong>URL</strong></label>
                                <p class="form-control-plaintext">
                                    <small class="text-break">{{ $auditLog->url ?? 'N/A' }}</small>
                                </p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label"><strong>User Agent</strong></label>
                                <p class="form-control-plaintext">
                                    <small class="text-break">{{ $auditLog->user_agent ?? 'N/A' }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Model Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-database me-2 text-accent"></i>Model Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Model Type</strong></label>
                                <p class="form-control-plaintext">
                                    @if($auditLog->model_type)
                                        <span class="badge bg-dark">{{ $auditLog->model_name }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Model ID</strong></label>
                                <p class="form-control-plaintext">
                                    @if($auditLog->model_id)
                                        <code>#{{ $auditLog->model_id }}</code>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="form-label"><strong>Changes Summary</strong></label>
                                <p class="form-control-plaintext">
                                    {{ $auditLog->changes_summary }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Details -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-exchange-alt me-2 text-accent"></i>Change Details</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="toggleJsonView()">
                            <i class="fas fa-code me-1"></i>Toggle JSON View
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="textView">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-danger mb-3">Old Values</h6>
                                    @if($auditLog->old_values)
                                        <div class="json-view" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($auditLog->old_values as $key => $value)
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong><br>
                                                    @if(is_array($value))
                                                        <pre class="mb-0"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                    @else
                                                        <span class="text-muted">{{ $value ?? 'NULL' }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No old values</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success mb-3">New Values</h6>
                                    @if($auditLog->new_values)
                                        <div class="json-view" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($auditLog->new_values as $key => $value)
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong><br>
                                                    @if(is_array($value))
                                                        <pre class="mb-0"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                    @else
                                                        <span class="text-muted">{{ $value ?? 'NULL' }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No new values</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div id="jsonView" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-danger mb-3">Old Values (JSON)</h6>
                                    <pre style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;"><code>{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success mb-3">New Values (JSON)</h6>
                                    <pre style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;"><code>{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .json-view {
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }
    pre {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        border-left: 4px solid var(--accent-color);
    }
    code {
        color: #e83e8c;
    }
</style>

<script>
    function toggleJsonView() {
        const textView = document.getElementById('textView');
        const jsonView = document.getElementById('jsonView');
        const button = event.target;
        
        if (textView.style.display === 'none') {
            textView.style.display = 'block';
            jsonView.style.display = 'none';
            button.innerHTML = '<i class="fas fa-code me-1"></i>Toggle JSON View';
        } else {
            textView.style.display = 'none';
            jsonView.style.display = 'block';
            button.innerHTML = '<i class="fas fa-text-width me-1"></i>Toggle Text View';
        }
    }
</script>
@endsection