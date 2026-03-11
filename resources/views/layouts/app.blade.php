<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Louies Dressed Chicken - Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary-color: #660B05; /* Your dark red color */
            --secondary-color: #5A0A04; /* Darker shade for gradient */
            --accent-color: #F4A300; /* Your yellow color */
            --text-light: #ffffff;
            --text-dark: #2c3e50;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--text-light);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0,0,0,0.2);
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 2px solid rgba(244, 163, 0, 0.3);
            background: rgba(0,0,0,0.1);
        }
        
        .logo-container {
            margin-bottom: 15px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color) 0%, #e69500 100%);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid var(--accent-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .logo img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .brand-text {
            text-align: center;
        }
        
        .brand-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .brand-tagline {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        .estd-badge {
            background: var(--accent-color);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-top: 8px;
            display: inline-block;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin: 5px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 14px 25px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-weight: 500;
            font-size: 15px;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(244, 163, 0, 0.15);
            border-left-color: var(--accent-color);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar-menu a.active {
            background-color: rgba(244, 163, 0, 0.25);
            border-left-color: var(--accent-color);
            color: var(--accent-color);
            font-weight: 600;
        }
        
        .sidebar-menu i {
            width: 25px;
            font-size: 18px;
            margin-right: 15px;
            color: var(--accent-color);
        }
        
        .sidebar-menu a.active i {
            color: var(--accent-color);
            transform: scale(1.1);
        }
        
        .menu-divider {
            height: 1px;
            background: rgba(244, 163, 0, 0.3);
            margin: 15px 25px;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .header {
            background: white;
            height: var(--header-height);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--accent-color);
        }
        
        .header-left h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: bold;
            border: 2px solid var(--primary-color);
        }
        
        .content {
            padding: 30px;
            background-color: #f8f9fa;
            min-height: calc(100vh - var(--header-height));
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border-top: 4px solid var(--accent-color);
            background: white;
        }
        
        .card-header {
            background: white;
            border-bottom: 2px solid #e9ecef;
            padding: 20px 25px;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .card-header h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
            font-size: 1.4rem;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Table Styles */
        .table {
            margin-bottom: 0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 16px 12px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            padding: 14px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            color: #495057;
        }
        
        .table tbody tr:hover {
            background-color: rgba(244, 163, 0, 0.05);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color) 0%, #e69500 100%);
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #e69500 0%, #d88a00 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(244, 163, 0, 0.4);
            color: var(--primary-color);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, var(--accent-color) 0%, #e69500 100%);
            border: none;
            color: var(--primary-color);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            background: linear-gradient(135deg, #e69500 0%, #d88a00 100%);
            color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(244, 163, 0, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #660B05 0%, #5A0A04 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #5A0A04 0%, #4D0803 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 11, 5, 0.3);
        }
        
        /* Status Badges */
        .status-active {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Form Styles */
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.3rem rgba(244, 163, 0, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        /* Search Box */
        .search-box {
            border: 2px solid #e9ecef;
            border-radius: 25px;
            padding: 12px 20px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .search-box:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.3rem rgba(244, 163, 0, 0.25);
        }
        
        /* Success Message */
        .alert-success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid var(--accent-color);
        }
        
        /* Section Headers */
        .section-header {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
            font-size: 1.2rem;
        }
        
        /* Icons */
        .text-accent {
            color: var(--accent-color) !important;
        }
        
        .text-primary-custom {
            color: var(--primary-color) !important;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .sidebar-header .brand-text,
            .sidebar-menu span {
                display: none;
            }
            
            .sidebar-menu i {
                margin-right: 0;
                font-size: 20px;
            }
            
            .sidebar-menu a {
                justify-content: center;
                padding: 15px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header-left h1 {
                font-size: 18px;
            }
        }
        
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 3px;
        }
        
        /* Animation for page load */
        .card {
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Logo placeholder styling */
        .logo-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color) 0%, #e69500 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            line-height: 1.2;
        }
        .status-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
    </style>
</head>
<body>
    <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo" style="width: 150px; height: 150px; border-radius: 0; background: transparent; border: none; box-shadow: none;">
                <!-- Your actual logo image -->
                <img src="{{ asset('images/logo.png') }}" alt="Louies Dressed Chicken" style="width: 200%; height: 200%; object-fit: contain; border-radius: 0;">
            </div>
        </div>
    </div>
        
        <nav class="sidebar-menu">
            <ul>
                <li>
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </li>
                 <li>
                      <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                      <i class="fas fa-truck"></i>
                      <span>Suppliers</span>
                   </a>
               </li>
                <li>
                    <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Billing</span>
                    </a>
                </li>
               <li>
                   <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                  <span>Stock</span>
                  </a>
                </li>
                
                
                <div class="menu-divider"></div>
                
                <li>
                    <a href="#" style="color: #ff6b6b;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>Employee Management</h1>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>Admin User</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile sidebar toggle (optional)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            // You can add mobile toggle functionality here if needed
        });
    </script>
</body>
</html>