

@extends('layouts.suppliers')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Supplier Details</h4>
        <div class="btn-group">
            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-edit me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this supplier?')">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Supplier ID</th>
                        <td>{{ $supplier->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $supplier->name }}</td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td>{{ $supplier->contact }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $supplier->email }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Address</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $supplier->address }}</p>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Created: {{ $supplier->created_at->format('M d, Y') }}
                        </small><br>
                        <small class="text-muted">
                            <i class="fas fa-calendar-check me-1"></i>
                            Last Updated: {{ $supplier->updated_at->format('M d, Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
            </a>
        </div>
    </div>
</div>
@endsection