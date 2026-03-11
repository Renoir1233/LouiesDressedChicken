<!-- resources/views/employees/index.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #2c3e50;"><i class="fas fa-user-plus me-2" style="color: #2196F3;"></i>Register Employee</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
            <i class="fas fa-plus me-2"></i>Add Employee
        </button>
    </div>
    
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" action="{{ route('employees.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box" placeholder="Search employees..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; margin-left: -2px;">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <h5 class="section-header"><i class="fas fa-list me-2 text-accent"></i>Employee List</h5>

        <!-- Employee List Table -->
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td><strong>{{ $employee->name }}</strong></td>
                        <td>{{ $employee->email }}</td>
                        <td><span class="badge bg-dark">{{ $employee->position }}</span></td>
                        <td>{{ $employee->address }}</td>
                        <td>{{ $employee->contact_no }}</td>
                        <td>
                            <span class="status-{{ strtolower($employee->status) }}">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>{{ $employee->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info view-employee-btn me-1" 
                                        data-bs-toggle="modal" data-bs-target="#viewEmployeeModal" 
                                        data-id="{{ $employee->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning edit-employee-btn me-1" 
                                        data-bs-toggle="modal" data-bs-target="#editEmployeeModal" 
                                        data-id="{{ $employee->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('employees.destroy', $employee->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this employee?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block text-accent"></i>
                            <p>No employees found.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                                <i class="fas fa-plus me-2"></i>Add First Employee
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Employee Modal -->
<div class="modal fade" id="createEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('employees.store') }}" method="POST" id="createEmployeeForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="createEmployeeErrors" class="alert alert-danger" style="display: none; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px; padding: 12px 15px;"></div>
                    @if($errors->any())
                    <div class="alert alert-danger" style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px; padding: 12px 15px;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position *</label>
                        <select class="form-select" name="position" required>
                            <option value="" disabled selected>Select Position</option>
                            <option value="Admin">Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Cashier">Cashier</option>
                            <option value="Driver">Driver</option>
                            <option value="Helper">Helper</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact No. *</label>
                        <input type="text" class="form-control" name="contact_no" id="create_contact_no" 
                               pattern="09[0-9]{9}" maxlength="11" 
                               placeholder="09XXXXXXXXX" 
                               title="Contact must be 11 digits and start with 09" required>
                        <small class="text-muted">Must be 11 digits starting with 09</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            <option value="Active" selected>Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editEmployeeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editEmployeeErrors" class="alert alert-danger" style="display: none; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px; padding: 12px 15px;"></div>
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position *</label>
                        <select class="form-select" id="edit_position" name="position" required>
                            <option value="" disabled>Select Position</option>
                            <option value="Admin">Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Cashier">Cashier</option>
                            <option value="Driver">Driver</option>
                            <option value="Helper">Helper</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact No. *</label>
                        <input type="text" class="form-control" id="edit_contact_no" name="contact_no" 
                               pattern="09[0-9]{9}" maxlength="11" 
                               placeholder="09XXXXXXXXX" 
                               title="Contact must be 11 digits and start with 09" required>
                        <small class="text-muted">Must be 11 digits starting with 09</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Employee Modal -->
<div class="modal fade" id="viewEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Name</th>
                        <td id="view_name"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="view_email"></td>
                    </tr>
                    <tr>
                        <th>Position</th>
                        <td id="view_position"></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td id="view_address"></td>
                    </tr>
                    <tr>
                        <th>Contact No.</th>
                        <td id="view_contact_no"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="view_status"></td>
                    </tr>
                    <tr>
                        <th>Created</th>
                        <td id="view_created"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contact validation function
    function validateContact(input, errorDivId) {
        const value = input.value;
        const errorDiv = document.getElementById(errorDivId);
        let errors = [];
        
        if (value.length !== 11) {
            errors.push('Contact number must be exactly 11 digits.');
        }
        if (!value.startsWith('09')) {
            errors.push('Contact number must start with 09.');
        }
        if (!/^[0-9]+$/.test(value)) {
            errors.push('Contact number must contain only numbers.');
        }
        
        if (errors.length > 0) {
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + errors.join('<br><i class="fas fa-exclamation-circle me-2"></i>');
            errorDiv.style.display = 'block';
            input.style.borderColor = '#dc3545';
            input.style.backgroundColor = '#fff5f5';
            return false;
        } else {
            errorDiv.style.display = 'none';
            input.style.borderColor = '';
            input.style.backgroundColor = '';
            return true;
        }
    }
    
    // Input restriction to numbers only
    function restrictToNumbers(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });
    }
    
    // Apply restrictions to contact inputs
    const createContactNo = document.getElementById('create_contact_no');
    const editContactNo = document.getElementById('edit_contact_no');
    
    if (createContactNo) restrictToNumbers(createContactNo);
    if (editContactNo) restrictToNumbers(editContactNo);
    
    // Form validation on submit
    const createForm = document.getElementById('createEmployeeForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            if (!validateContact(createContactNo, 'createEmployeeErrors')) {
                e.preventDefault();
            }
        });
    }
    
    const editForm = document.getElementById('editEmployeeForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (!validateContact(editContactNo, 'editEmployeeErrors')) {
                e.preventDefault();
            }
        });
    }
    
    // Clear errors when modal is closed
    document.getElementById('createEmployeeModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('createEmployeeErrors').style.display = 'none';
        if (createContactNo) {
            createContactNo.style.borderColor = '';
            createContactNo.style.backgroundColor = '';
        }
    });
    
    document.getElementById('editEmployeeModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('editEmployeeErrors').style.display = 'none';
        if (editContactNo) {
            editContactNo.style.borderColor = '';
            editContactNo.style.backgroundColor = '';
        }
    });

    // View Employee
    document.querySelectorAll('.view-employee-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/employees/${id}/get-employee`)
                .then(res => res.json())
                .then(employee => {
                    document.getElementById('view_name').textContent = employee.name;
                    document.getElementById('view_email').textContent = employee.email;
                    document.getElementById('view_position').innerHTML = `<span class="badge bg-dark">${employee.position}</span>`;
                    document.getElementById('view_address').textContent = employee.address;
                    document.getElementById('view_contact_no').textContent = employee.contact_no;
                    document.getElementById('view_status').innerHTML = employee.status === 'Active' 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-secondary">Inactive</span>';
                    document.getElementById('view_created').textContent = new Date(employee.created_at).toLocaleString();
                });
        });
    });

    // Edit Employee
    document.querySelectorAll('.edit-employee-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/employees/${id}/get-employee`)
                .then(res => res.json())
                .then(employee => {
                    document.getElementById('edit_name').value = employee.name;
                    document.getElementById('edit_email').value = employee.email;
                    document.getElementById('edit_position').value = employee.position;
                    document.getElementById('edit_address').value = employee.address;
                    document.getElementById('edit_contact_no').value = employee.contact_no;
                    document.getElementById('edit_status').value = employee.status;
                    document.getElementById('editEmployeeForm').action = `/employees/${id}`;
                });
        });
    });
    
    // Auto-open modal if there are validation errors
    @if($errors->any())
        var createModal = new bootstrap.Modal(document.getElementById('createEmployeeModal'));
        createModal.show();
    @endif
});
</script>
@endsection