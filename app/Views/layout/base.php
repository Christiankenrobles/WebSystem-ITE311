<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Supplies Inventory & Sales System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
        }

        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 20px;
        }

        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            position: sticky;
            top: 0;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 0;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            background-color: #f0f0f0;
            color: var(--primary);
            border-left-color: var(--primary);
            padding-left: 25px;
        }

        .sidebar .nav-link.active {
            background-color: var(--primary);
            color: white;
            border-left-color: var(--secondary);
            padding-left: 17px;
        }

        .main-content {
            padding: 30px;
        }

        .page-title {
            margin-bottom: 30px;
            color: #333;
        }

        .page-title h1 {
            font-weight: 600;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .card {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 8px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                min-height: auto;
            }

            .main-content {
                padding: 15px;
            }
        }
    </style>
    <?php if (isset($additionalCss)): ?>
        <?= $additionalCss ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-boxes"></i> Supplies Inventory & Sales
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?= session()->get('username') ?? 'User' ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-key"></i> Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/auth/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="nav flex-column">
                    <a class="nav-link <?= uri_string() === '' || uri_string() === '/' || strpos(uri_string(), 'dashboard') === 0 ? 'active' : '' ?>" href="/">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    
                    <div class="nav-section mt-3 px-3 text-muted text-uppercase small">Operations</div>
                    
                    <a class="nav-link <?= strpos(uri_string(), 'pos') === 0 ? 'active' : '' ?>" href="/pos">
                        <i class="fas fa-cash-register"></i> Point of Sale
                    </a>
                    <a class="nav-link <?= strpos(uri_string(), 'sales') === 0 ? 'active' : '' ?>" href="/sales">
                        <i class="fas fa-receipt"></i> Sales
                    </a>

                    <div class="nav-section mt-3 px-3 text-muted text-uppercase small">Inventory</div>
                    
                    <a class="nav-link <?= strpos(uri_string(), 'products') === 0 ? 'active' : '' ?>" href="/products">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a class="nav-link <?= strpos(uri_string(), 'suppliers') === 0 ? 'active' : '' ?>" href="/suppliers">
                        <i class="fas fa-industry"></i> Suppliers
                    </a>
                    <a class="nav-link <?= strpos(uri_string(), 'purchase-orders') === 0 ? 'active' : '' ?>" href="/purchase-orders">
                        <i class="fas fa-shopping-cart"></i> Purchase Orders
                    </a>

                    <div class="nav-section mt-3 px-3 text-muted text-uppercase small">Reporting</div>
                    
                    <a class="nav-link <?= strpos(uri_string(), 'reports') === 0 ? 'active' : '' ?>" href="/reports">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>

                    <hr class="my-3">

                    <div class="nav-section px-3 text-muted text-uppercase small">Administration</div>
                    
                    <a class="nav-link <?= strpos(uri_string(), 'admin/users') === 0 ? 'active' : '' ?>" href="/admin/users">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a class="nav-link <?= strpos(uri_string(), 'admin/settings') === 0 ? 'active' : '' ?>" href="/admin/settings">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?= session('warning') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Page Title -->
                <?php if (isset($title)): ?>
                    <div class="page-title">
                        <h1><?= $title ?></h1>
                        <?php if (isset($subtitle)): ?>
                            <p class="text-muted"><?= $subtitle ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Page Content -->
                <?php echo $this->renderSection('content'); ?>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center py-4 mt-5">
        <div class="container-fluid">
            <small class="text-muted">
                &copy; <?= date('Y') ?> Supplies Inventory & Sales System. All rights reserved.
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <?php if (isset($additionalJs)): ?>
        <?= $additionalJs ?>
    <?php endif; ?>
</body>
</html>
