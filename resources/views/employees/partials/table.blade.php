{{-- resources/views/employees/partials/table.blade.php --}}
@if($employees->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td><strong>{{ $employee->name }}</strong></td>
                    <td>{{ $employee->email }}</td>
                    <td><span class="badge bg-dark">{{ $employee->position }}</span></td>
                    <td>{{ Str::limit($employee->address, 50) }}</td>
                    <td>{{ $employee->contact_no }}</td>
                    <td>
                        <span class="status-{{ strtolower($employee->status) }}">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>{{ $employee->status }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-edit me-1 edit-employee-btn" 
                                    data-id="{{ $employee->id }}"
                                    data-name="{{ $employee->name }}">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            <button type="button" class="btn btn-delete delete-employee-btn" 
                                    data-id="{{ $employee->id }}"
                                    data-name="{{ $employee->name }}">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center text-muted py-5">
        <i class="fas fa-users fa-3x mb-3 d-block text-accent"></i>
        @if(request()->has('search') && request('search'))
            <p>No employees found for "{{ request('search') }}"</p>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Clear Search
            </a>
        @else
            <p>No employees found.</p>
            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                <i class="fas fa-plus me-2"></i>Add First Employee
            </button>
        @endif
    </div>
@endif