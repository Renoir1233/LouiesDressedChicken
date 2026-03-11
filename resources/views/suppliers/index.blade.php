

@extends('layouts.suppliers')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #2c3e50;">Supplier Management</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
            <i class="fas fa-plus me-2"></i>Add New Supplier
        </button>
    </div>
    <div class="card-body">
        @if($suppliers->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ Str::limit($supplier->address, 50) }}</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-primary me-1 view-supplier-btn" 
                                            data-id="{{ $supplier->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewSupplierModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-edit me-1 edit-supplier-btn" 
                                            data-id="{{ $supplier->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editSupplierModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $suppliers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No suppliers found</h5>
                <p class="text-muted">Get started by adding your first supplier.</p>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                    <i class="fas fa-plus me-2"></i>Add New Supplier
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('suppliers.store') }}" method="POST" id="createSupplierForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="createSupplierErrors" class="alert alert-danger" style="display: none; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px; padding: 12px 15px;"></div>
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
                        <label class="form-label">Contact *</label>
                        <input type="text" class="form-control" name="contact" id="create_contact" 
                               pattern="09[0-9]{9}" maxlength="11" 
                               placeholder="09XXXXXXXXX" 
                               title="Contact must be 11 digits and start with 09" required>
                        <small class="text-muted">Must be 11 digits starting with 09</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea class="form-control" name="address" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSupplierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editSupplierErrors" class="alert alert-danger" style="display: none; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px; padding: 12px 15px;"></div>
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact *</label>
                        <input type="text" class="form-control" id="edit_contact" name="contact" 
                               pattern="09[0-9]{9}" maxlength="11" 
                               placeholder="09XXXXXXXXX" 
                               title="Contact must be 11 digits and start with 09" required>
                        <small class="text-muted">Must be 11 digits starting with 09</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Supplier is Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Supplier Modal -->
<div class="modal fade" id="viewSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Name</th>
                        <td id="view_name"></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td id="view_contact"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="view_email"></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td id="view_address"></td>
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
    const createContact = document.getElementById('create_contact');
    const editContact = document.getElementById('edit_contact');
    
    if (createContact) restrictToNumbers(createContact);
    if (editContact) restrictToNumbers(editContact);
    
    // Form validation on submit
    const createForm = document.getElementById('createSupplierForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            if (!validateContact(createContact, 'createSupplierErrors')) {
                e.preventDefault();
            }
        });
    }
    
    const editForm = document.getElementById('editSupplierForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (!validateContact(editContact, 'editSupplierErrors')) {
                e.preventDefault();
            }
        });
    }
    
    // Clear errors when modal is closed
    document.getElementById('createSupplierModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('createSupplierErrors').style.display = 'none';
        if (createContact) {
            createContact.style.borderColor = '';
            createContact.style.backgroundColor = '';
        }
    });
    
    document.getElementById('editSupplierModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('editSupplierErrors').style.display = 'none';
        if (editContact) {
            editContact.style.borderColor = '';
            editContact.style.backgroundColor = '';
        }
    });

    // View Supplier
    document.querySelectorAll('.view-supplier-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/suppliers/${id}/get-supplier`)
                .then(res => res.json())
                .then(supplier => {
                    document.getElementById('view_name').textContent = supplier.name;
                    document.getElementById('view_contact').textContent = supplier.contact;
                    document.getElementById('view_email').textContent = supplier.email;
                    document.getElementById('view_address').textContent = supplier.address;
                    document.getElementById('view_status').innerHTML = supplier.is_active == 1 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-secondary">Inactive</span>';
                    document.getElementById('view_created').textContent = new Date(supplier.created_at).toLocaleString();
                });
        });
    });

    // Edit Supplier
    document.querySelectorAll('.edit-supplier-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            fetch(`/suppliers/${id}/get-supplier`)
                .then(res => res.json())
                .then(supplier => {
                    document.getElementById('edit_name').value = supplier.name;
                    document.getElementById('edit_contact').value = supplier.contact;
                    document.getElementById('edit_email').value = supplier.email;
                    document.getElementById('edit_address').value = supplier.address;
                    document.getElementById('edit_is_active').checked = supplier.is_active == 1;
                    document.getElementById('editSupplierForm').action = `/suppliers/${id}`;
                });
        });
    });
    
    // Auto-open modal if there are validation errors
    @if($errors->any())
        var createModal = new bootstrap.Modal(document.getElementById('createSupplierModal'));
        createModal.show();
    @endif
});
</script>
@endsection