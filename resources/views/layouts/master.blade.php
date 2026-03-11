<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Louies Dressed Chicken - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            
            /* Enhanced Color Palette */
            --primary-color: #660B05;      /* Main red */
            --secondary-color: #8B0000;    /* Darker red for contrast */
            --accent-color: #F4A300;       /* Golden yellow */
            --accent-light: #FFD166;       /* Lighter gold */
            --neutral-light: #F8F9FA;      /* Light background */
            --neutral-dark: #2C3E50;       /* Dark text */
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            
            /* Border Variables */
            --border-radius-sm: 6px;
            --border-radius-md: 10px;
            --border-radius-lg: 15px;
            --border-width: 2px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.15);
            --shadow-lg: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--neutral-dark);
            overflow-x: hidden;
        }
        
        /* Enhanced Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, 
                var(--primary-color) 0%, 
                var(--secondary-color) 100%);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            border-right: var(--border-width) solid var(--accent-color);
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 2px solid rgba(244, 163, 0, 0.4);
            background: linear-gradient(to right, 
                rgba(0,0,0,0.1) 0%, 
                rgba(0,0,0,0.2) 100%);
            position: relative;
        }
        
        .logo-container {
            margin-bottom: 15px;
            position: relative;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            border: 3px solid var(--accent-color);
            border-radius: 50%;
            background: linear-gradient(135deg, 
                var(--accent-color) 0%, 
                #e69500 100%);
            box-shadow: 0 4px 15px rgba(244, 163, 0, 0.4);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .logo:hover {
            transform: rotate(5deg);
            box-shadow: 0 6px 20px rgba(244, 163, 0, 0.6);
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .brand-text {
            text-align: center;
            padding: 10px;
            background: rgba(0,0,0,0.2);
            border-radius: var(--border-radius-md);
            border: 1px solid rgba(244, 163, 0, 0.2);
        }
        
        .brand-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .brand-tagline {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.9);
            font-weight: 400;
            letter-spacing: 0.8px;
        }
        
        .estd-badge {
            background: linear-gradient(135deg, 
                var(--accent-color) 0%, 
                #e69500 100%);
            color: var(--primary-color);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-top: 8px;
            display: inline-block;
            border: 1px solid var(--primary-color);
            box-shadow: var(--shadow-sm);
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
            margin: 3px 15px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            font-size: 15px;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-menu a:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255,255,255,0.1), 
                transparent);
            transition: all 0.5s ease;
        }
        
        .sidebar-menu a:hover:before {
            left: 100%;
        }
        
        .sidebar-menu a:hover {
            background: linear-gradient(90deg, 
                rgba(244, 163, 0, 0.15) 0%, 
                rgba(244, 163, 0, 0.05) 100%);
            border-left-color: var(--accent-color);
            color: white;
            transform: translateX(8px);
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(244, 163, 0, 0.2);
        }
        
        .sidebar-menu a.active {
            background: linear-gradient(90deg, 
                rgba(244, 163, 0, 0.25) 0%, 
                rgba(244, 163, 0, 0.15) 100%);
            border-left-color: var(--accent-color);
            color: var(--accent-color);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(244, 163, 0, 0.3);
        }
        
        .sidebar-menu i {
            width: 25px;
            font-size: 18px;
            margin-right: 15px;
            color: var(--accent-color);
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a.active i {
            color: var(--accent-color);
            transform: scale(1.2);
        }
        
        .menu-divider {
            height: 1px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(244, 163, 0, 0.4) 50%, 
                transparent 100%);
            margin: 20px 25px;
            border: none;
        }
        
        /* Enhanced Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .header {
            background: linear-gradient(90deg, 
                white 0%, 
                #f8f9fa 100%);
            height: var(--header-height);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--accent-color);
            border-radius: 0 0 var(--border-radius-md) var(--border-radius-md);
        }
        
        .header-left h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--primary-color);
            font-weight: 500;
            padding: 8px 15px;
            border-radius: var(--border-radius-lg);
            background: white;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .user-info:hover {
            border-color: var(--accent-color);
            box-shadow: var(--shadow-sm);
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, 
                var(--accent-color) 0%, 
                #e69500 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: bold;
            border: 2px solid var(--accent-color);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
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
            font-weight: 500;
        }
        
        .content {
            padding: 30px;
            background: linear-gradient(135deg, 
                #f5f7fa 0%, 
                #e4e8f0 100%);
            min-height: calc(100vh - var(--header-height));
        }
        
        /* Enhanced Card Styles */
        .card {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            border-top: 4px solid var(--accent-color);
            background: white;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent-color);
        }
        
        .card-header {
            background: linear-gradient(90deg, 
                white 0%, 
                #f8f9fa 100%);
            border-bottom: 2px solid var(--accent-light);
            padding: 20px 25px;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .card-header h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Enhanced Table Styles */
        .table-container {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            background: linear-gradient(180deg, 
                var(--primary-color) 0%, 
                var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 18px 15px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--accent-color);
            position: relative;
        }
        
        .table th:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .table td {
            padding: 16px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            color: #495057;
            background: white;
            transition: all 0.2s ease;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background: linear-gradient(90deg, 
                rgba(244, 163, 0, 0.05) 0%, 
                rgba(244, 163, 0, 0.02) 100%);
            transform: translateX(5px);
        }
        
        .table tbody tr:hover td {
            background: transparent;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        /* Enhanced Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, 
                var(--accent-color) 0%, 
                #e69500 100%);
            border: 2px solid var(--accent-color);
            padding: 10px 25px;
            border-radius: var(--border-radius-md);
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--primary-color);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, 
                #e69500 0%, 
                #d88a00 100%);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: #e69500;
            color: var(--primary-color);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, 
                var(--accent-color) 0%, 
                #e69500 100%);
            border: 1px solid var(--accent-color);
            color: var(--primary-color);
            padding: 8px 16px;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-edit:hover {
            background: linear-gradient(135deg, 
                #e69500 0%, 
                #d88a00 100%);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: #e69500;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, 
                var(--danger-color) 0%, 
                #c82333 100%);
            border: 1px solid var(--danger-color);
            color: white;
            padding: 8px 16px;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, 
                #c82333 0%, 
                #bd2130 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: #c82333;
        }
        
        /* Enhanced Status Badges */
        .badge {
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: 1px solid transparent;
            box-shadow: var(--shadow-sm);
        }
        
        .status-active {
            background: linear-gradient(135deg, 
                var(--success-color) 0%, 
                #218838 100%);
            color: white;
            border-color: #28a745;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, 
                var(--danger-color) 0%, 
                #c82333 100%);
            color: white;
            border-color: #dc3545;
        }
        
        .status-warning {
            background: linear-gradient(135deg, 
                var(--warning-color) 0%, 
                #e0a800 100%);
            color: white;
            border-color: #ffc107;
        }
        
        /* Enhanced Form Styles */
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius-md);
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
            background: white;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.3rem rgba(244, 163, 0, 0.25),
                        inset 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        /* Enhanced Search Box */
        .search-box {
            border: 2px solid #e9ecef;
            border-radius: 25px;
            padding: 12px 20px 12px 45px;
            transition: all 0.3s ease;
            font-size: 14px;
            background: white url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23660B05" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat 15px center;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .search-box:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.3rem rgba(244, 163, 0, 0.25),
                        inset 0 2px 4px rgba(0,0,0,0.05);
        }
        
        /* Enhanced Alert Messages */
        .alert {
            border: 2px solid transparent;
            border-radius: var(--border-radius-md);
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid;
        }
        
        .alert-success {
            background: linear-gradient(135deg, 
                var(--success-color) 0%, 
                #218838 100%);
            color: white;
            border-color: #28a745;
            border-left-color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, 
                var(--danger-color) 0%, 
                #c82333 100%);
            color: white;
            border-color: #dc3545;
            border-left-color: #721c24;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, 
                var(--warning-color) 0%, 
                #e0a800 100%);
            color: white;
            border-color: #ffc107;
            border-left-color: #856404;
        }
        
        .alert-info {
            background: linear-gradient(135deg, 
                var(--info-color) 0%, 
                #138496 100%);
            color: white;
            border-color: #17a2b8;
            border-left-color: #0c5460;
        }
        
        /* Section Headers */
        .section-header {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--accent-color);
            font-size: 1.3rem;
            position: relative;
            display: inline-block;
        }
        
        .section-header:after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
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
            
            .header {
                padding: 0 15px;
            }
            
            .header-left h1 {
                font-size: 18px;
            }
            
            .user-details {
                display: none;
            }
            
            .content {
                padding: 15px;
            }
        }
        
        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, 
                var(--accent-color) 0%, 
                #e69500 100%);
            border-radius: 4px;
            border: 1px solid rgba(0,0,0,0.2);
        }
        
        /* Animations */
        .card {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Logo placeholder styling */
        .logo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, 
                var(--accent-color) 0%, 
                #e69500 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            line-height: 1.2;
            border: 3px solid var(--accent-color);
            box-shadow: 0 4px 15px rgba(244, 163, 0, 0.4);
        }
        
        /* Admin section styling */
        .admin-section {
            background: linear-gradient(90deg, 
                rgba(0,0,0,0.1) 0%, 
                rgba(0,0,0,0.15) 100%);
            margin: 10px 0;
            padding: 8px 0;
            border-radius: var(--border-radius-md);
            border: 1px solid rgba(244, 163, 0, 0.2);
        }
        
        /* Loading spinner */
        .spinner-border {
            color: var(--accent-color);
        }
        
        /* Custom borders for containers */
        .border-custom {
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius-md);
            padding: 20px;
            background: white;
        }
        
        /* Stats cards */
        .stat-card {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius-lg);
            padding: 20px;
            background: white;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            border-color: var(--accent-color);
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Louies Dressed Chicken" 
                         onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\'logo-placeholder\'>L<br>D<br>C</div>';">
                </div>
            </div>
            <div class="brand-text">
                <div class="brand-name">Louies Dressed Chicken</div>
                <div class="brand-tagline">Premium Quality Since 1995</div>
                <div class="estd-badge">EST. 1995</div>
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
                        <span>Employees</span>
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
                        <span>Inventory</span>
                    </a>
                </li>
                
                <!-- Authentication Section -->
                @auth
                    <!-- Admin Management Section -->
                    @if(auth()->user()->hasPermission('users.*') || auth()->user()->hasPermission('audit-logs.*'))
                        <div class="menu-divider"></div>
                        <div class="admin-section">
                            <div style="padding: 0 20px 5px; font-size: 11px; color: var(--accent-color); text-transform: uppercase; letter-spacing: 1px;">
                                <i class="fas fa-cog me-2"></i>Admin
                            </div>
                            
                            @if(auth()->user()->hasPermission('users.*'))
                            <li>
                                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="fas fa-users-cog"></i>
                                    <span>User Management</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('roles.*'))
                            <li>
                                <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-shield"></i>
                                    <span>Roles</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('audit-logs.*'))
                            <li>
                                <a href="{{ route('audit-logs.index') }}" class="{{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span>Audit Logs</span>
                                </a>
                            </li>
                            @endif
                        </div>
                    @endif
                    
                    <!-- User Profile Section -->
                    <div class="menu-divider"></div>
                    
                    <li>
                        <a href="{{ route('profile') }}" class="{{ request()->is('profile') ? 'active' : '' }}">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    
                    <!-- Logout -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) document.getElementById('logout-form').submit();" 
                           style="color: #ff6b6b !important;">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                @endauth
                
                <!-- Guest Section -->
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
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        @auth
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                                     onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--accent-color)0%,#e69500 100%);color:var(--primary-color);font-weight:bold;font-size:18px;border-radius:50%;\'>{{ substr(auth()->user()->name, 0, 1) }}</div>';">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--accent-color)0%,#e69500 100%);color:var(--primary-color);font-weight:bold;font-size:18px;border-radius:50%;">
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(1);"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(1);"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(1);"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(1);"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const headerTitle = document.querySelector('.header-left h1');
            
            // Mobile menu toggle
            const menuToggle = document.createElement('button');
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.className = 'menu-toggle btn btn-primary d-md-none';
            menuToggle.style.cssText = 'position: fixed; top: 15px; left: 15px; z-index: 1100;';
            document.body.appendChild(menuToggle);
            
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
            
            // Update page title
            const pageTitle = document.querySelector('h1.section-header, .card-header h4, .content h1');
            if (pageTitle && headerTitle) {
                headerTitle.textContent = pageTitle.textContent || 'Dashboard';
            }
            
            // Auto-dismiss alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            // Active link highlighting
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll('.sidebar-menu a');
            
            menuLinks.forEach(link => {
                const linkPath = link.getAttribute('href');
                if (linkPath && 
                    (currentPath === linkPath || 
                    (linkPath !== '/' && currentPath.startsWith(linkPath)) ||
                    (linkPath === '/dashboard' && currentPath === '/'))) {
                    link.classList.add('active');
                }
            });
            
            // Add loading state to buttons
            document.querySelectorAll('button[type="submit"]').forEach(button => {
                button.addEventListener('click', function() {
                    if (!this.classList.contains('disabled')) {
                        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                        this.classList.add('disabled');
                    }
                });
            });
            
            // Update browser title dynamically
            const routeTitles = {
                '/dashboard': 'Dashboard',
                '/orders': 'Order Management',
                '/suppliers': 'Supplier Management',
                '/employees': 'Employee Management',
                '/billing': 'Billing',
                '/inventory': 'Inventory Management',
                '/users': 'User Management',
                '/roles': 'Role Management',
                '/audit-logs': 'Audit Logs',
                '/profile': 'My Profile'
            };
            
            let pageTitleText = 'Louies Dressed Chicken';
            for (const [route, title] of Object.entries(routeTitles)) {
                if (currentPath.startsWith(route)) {
                    pageTitleText = title;
                    break;
                }
            }
            
            document.title = pageTitleText + ' | Louies Dressed Chicken';
        });
    </script>
    
    @yield('scripts')
</body>
</html>