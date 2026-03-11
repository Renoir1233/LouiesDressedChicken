<!-- resources/views/layouts/inventory.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Louies Dressed Chicken - Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Add DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
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
            overflow: hidden;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 14px;
        }
        
        .user-role {
            font-size: 12px;
            color: #6c757d;
        }
        
        .content {
            padding: 30px;
            background-color: #f8f9fa;
            min-height: calc(100vh - var(--header-height));
        }
        
        /* Card Styles */
        .card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            border-top: 4px solid var(--accent-color);
            background: white;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
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
            border: 1px solid #e9ecef;
        }
        
        .table th {
            background-color: var(--primary-color);
            color: white;
            border: 1px solid #e9ecef;
            padding: 16px 12px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            padding: 14px 12px;
            vertical-align: middle;
            border: 1px solid #e9ecef;
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
            box-shadow: 0 4px 12px rgba(244, 163, 0, 0.2);
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
            box-shadow: 0 3px 8px rgba(244, 163, 0, 0.2);
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
            box-shadow: 0 3px 8px rgba(102, 11, 5, 0.2);
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .search-box:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.3rem rgba(244, 163, 0, 0.25);
        }
        
        /* Alert Styles */
        .alert-success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid var(--accent-color);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid var(--accent-color);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid var(--accent-color);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
        }
        
        .alert-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid var(--accent-color);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.2);
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
        
        /* Additional styles for inventory sub-navigation */
        .nav-tabs-custom {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
            background: white;
            border-radius: 8px;
            padding: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 12px 20px;
            border-radius: 6px;
            margin: 0 2px;
            transition: all 0.3s ease;
        }
        
        .nav-tabs-custom .nav-link:hover {
            background-color: rgba(244, 163, 0, 0.1);
            color: var(--primary-color);
        }
        
        .nav-tabs-custom .nav-link.active {
            background: linear-gradient(135deg, var(--accent-color) 0%, #e69500 100%);
            color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(244, 163, 0, 0.2);
        }
        
        .stats-card {
            transition: transform 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }
        
        .fifo-batch {
            border-left: 4px solid #28a745;
            padding-left: 15px;
            margin-bottom: 10px;
            background: rgba(40, 167, 69, 0.05);
            padding: 10px;
            border-radius: 0 6px 6px 0;
        }
        
        .fifo-batch.expiring {
            border-left-color: #ffc107;
            background: rgba(255, 193, 7, 0.05);
        }
        
        .fifo-batch.expired {
            border-left-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }
        
        /* Status warning for inventory */
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
        
        /* Logout link styling */
        .logout-link {
            color: #ff6b6b !important;
        }
        
        .logout-link:hover {
            color: #ff5252 !important;
            background-color: rgba(255, 107, 107, 0.1) !important;
        }
        
        /* Profile link styling */
        .profile-link {
            color: rgba(255,255,255,0.9) !important;
        }
        
        .profile-link:hover {
            color: var(--accent-color) !important;
        }
        
        /* Admin section styling */
        .admin-section {
            background: rgba(0,0,0,0.1);
            margin: 10px 0;
            padding: 5px 0;
            border-radius: 8px;
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
            
            .user-details {
                display: none;
            }
            
            /* Inventory tabs responsive */
            .nav-tabs-custom {
                overflow-x: auto;
                white-space: nowrap;
                flex-wrap: nowrap;
                padding-bottom: 5px;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 10px 15px;
                font-size: 14px;
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
        
        /* DataTables customization */
        .dataTables_wrapper .dt-buttons .btn {
            background: linear-gradient(135deg, var(--accent-color) 0%, #e69500 100%);
            border: none;
            color: var(--primary-color);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            margin: 2px;
        }
        
        .dataTables_wrapper .dt-buttons .btn:hover {
            background: linear-gradient(135deg, #e69500 0%, #d88a00 100%);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(244, 163, 0, 0.3);
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
                    <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
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
                    <a href="{{ url('/billing') }}" class="{{ request()->is('billing') ? 'active' : '' }}">
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
                
                <!-- Authentication Section -->
                @auth
                    <!-- Admin Management Section (only for users with permissions) -->
                    @if(auth()->user()->hasPermission('users.*') || auth()->user()->hasPermission('audit-logs.*'))
                        <div class="menu-divider"></div>
                        
                        <!-- User Management -->
                        @if(auth()->user()->hasPermission('users.*'))
                        <li>
                            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i>
                                <span>User Management</span>
                            </a>
                        </li>
                        @endif
                        
                        <!-- Roles Management -->
                        @if(auth()->user()->hasPermission('roles.*'))
                        <li>
                            <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="fas fa-user-shield"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                        @endif
                        
                        <!-- Audit Logs -->
                        @if(auth()->user()->hasPermission('audit-logs.*'))
                        <li>
                            <a href="{{ route('audit-logs.index') }}" class="{{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Audit Logs</span>
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    <!-- User Profile Section -->
                    <div class="menu-divider"></div>
                    
                    <li>
                        <a href="{{ route('profile') }}" class="{{ request()->is('profile') ? 'active' : '' }} profile-link">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    
                    <!-- Logout -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                           class="logout-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                @endauth
                
                <!-- Guest Section (if not authenticated) -->
                @guest
                    <div class="menu-divider"></div>
                    
                    <li>
                        <a href="{{ route('login') }}" class="{{ request()->is('login') ? 'active' : '' }}">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    </li>
                @endguest
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>Inventory Management</h1>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        @auth
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                                     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--accent-color) 0%, #e69500 100%); color: var(--primary-color); font-weight: bold; font-size: 16px; border-radius: 50%;">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        @else
                            <i class="fas fa-user"></i>
                        @endauth
                    </div>
                    <div class="user-details">
                        @auth
                            <span class="user-name">{{ auth()->user()->name }}</span>
                            <small class="user-role">{{ auth()->user()->role_name }}</small>
                        @else
                            <span class="user-name">Guest</span>
                            <small class="user-role">Not logged in</small>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Session Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Inventory Navigation -->
            @if(request()->routeIs('inventory.*'))
<div class="card mb-4">
    <div class="card-body">
        <ul class="nav nav-tabs-custom">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}" 
                   href="{{ route('inventory.index') }}">
                    <i class="fas fa-boxes me-2"></i>Main Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.stock-in.*') ? 'active' : '' }}" 
                   href="{{ route('inventory.stock-in.index') }}">
                    <i class="fas fa-arrow-down me-2"></i>Stock In
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.stock-out.*') ? 'active' : '' }}" 
                   href="{{ route('inventory.stock-out.index') }}">
                    <i class="fas fa-arrow-up me-2"></i>Stock Out
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.movement-history') ? 'active' : '' }}" 
                   href="{{ route('inventory.movement-history') }}">
                    <i class="fas fa-history me-2"></i>Movement History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.low-stock') ? 'active' : '' }}" 
                   href="{{ route('inventory.low-stock') }}">
                    <i class="fas fa-exclamation-triangle me-2"></i>Low Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.out-of-stock') ? 'active' : '' }}" 
                   href="{{ route('inventory.out-of-stock') }}">
                    <i class="fas fa-times-circle me-2"></i>Out of Stock
                </a>
            </li>
            <li class="nav-item">
                 <a class="nav-link {{ request()->routeIs('inventory.expiring-soon') ? 'active' : '' }}" 
                    href="{{ route('inventory.expiring-soon') }}">
                     <i class="fas fa-clock me-2"></i>Expiring Soon
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link {{ request()->routeIs('inventory.report') ? 'active' : '' }}" 
                    href="{{ route('inventory.report') }}">
                     <i class="fas fa-chart-bar me-2"></i>Inventory Report
                 </a>
             </li>
            </ul>
            </div>
            </div>
            @endif
            
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add jQuery for DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Add DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    
    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const header = document.querySelector('.header-left h1');
            
            // Update page title from content
            const pageTitle = document.querySelector('.content h1, .content h2, .content h3, .card-header h4');
            if (pageTitle && header) {
                header.textContent = pageTitle.textContent || 'Louies Dressed Chicken';
            }
            
            // Auto-hide sidebar on mobile when clicking outside
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !event.target.closest('.menu-toggle')) {
                        sidebar.classList.remove('active');
                    }
                }
            });
            
            // Logout confirmation
            const logoutLink = document.querySelector('.logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to logout?')) {
                        e.preventDefault();
                    }
                });
            }
            
            // Active link highlighting for sidebar
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll('.sidebar-menu a');
            
            menuLinks.forEach(link => {
                const linkPath = link.getAttribute('href');
                if (currentPath === linkPath || 
                    (linkPath !== '/' && currentPath.startsWith(linkPath)) ||
                    link.classList.contains('active')) {
                    link.classList.add('active');
                }
            });
            
            // Initialize DataTables if table exists
            if (typeof $.fn.DataTable !== 'undefined') {
                $('.datatable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    pageLength: 25,
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "_MENU_ records per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries found",
                        infoFiltered: "(filtered from _MAX_ total entries)"
                    }
                });
            }
            
            // Date pickers
            $('.datepicker').each(function() {
                this.type = 'date';
            });
            
            // Update page title based on route
            const routeTitles = {
                '/dashboard': 'Dashboard',
                '/orders': 'Order Management',
                '/orders/create': 'Create Order',
                '/suppliers': 'Supplier Management',
                '/employees': 'Employee Management',
                '/billing': 'Billing & Payments',
                '/inventory': 'Inventory Management',
                '/inventory/stock-in': 'Stock In',
                '/inventory/stock-out': 'Stock Out',
                '/inventory/movement-history': 'Movement History',
                '/inventory/low-stock': 'Low Stock Alerts',
                '/inventory/out-of-stock': 'Out of Stock',
                '/inventory/expiring-soon': 'Expiring Soon',
                '/inventory/report': 'Inventory Report',
                '/users': 'User Management',
                '/roles': 'Role Management',
                '/audit-logs': 'Audit Logs',
                '/profile': 'My Profile'
            };
            
            const routeSubTitles = {
                '/inventory': 'Main Stock',
                '/inventory/stock-in': 'Stock In Records',
                '/inventory/stock-out': 'Stock Out Records',
                '/inventory/movement-history': 'Stock Movement History',
                '/inventory/low-stock': 'Low Stock Items',
                '/inventory/out-of-stock': 'Out of Stock Items',
                '/inventory/expiring-soon': 'Expiring Items',
                '/inventory/report': 'Inventory Report & Analytics'
            };
            
            let pageTitle = 'Louies Dressed Chicken';
            
            // Find matching route for header title
            for (const [route, title] of Object.entries(routeTitles)) {
                if (currentPath.startsWith(route)) {
                    pageTitle = title;
                    break;
                }
            }
            
            // Update header title
            if (header) {
                header.textContent = pageTitle;
            }
            
            // Update browser tab title
            document.title = pageTitle + ' | Louies Dressed Chicken';
            
            // Add current page indicator to inventory sub-nav
            const inventoryNavLinks = document.querySelectorAll('.nav-tabs-custom .nav-link');
            inventoryNavLinks.forEach(link => {
                const linkPath = link.getAttribute('href');
                if (currentPath.startsWith(linkPath.replace(route('inventory.index'), '/inventory'))) {
                    link.classList.add('active');
                }
            });
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>