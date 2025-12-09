<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Supplies Inventory & Sales System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        }

        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: #f0f0f0;
            color: #667eea;
            padding-left: 30px;
        }

        .sidebar .nav-link.active {
            background-color: #667eea;
            color: white;
            border-left: 4px solid #764ba2;
            padding-left: 16px;
        }

        .main-content {
            padding: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }

        .stat-card.warning .stat-value {
            color: #ffc107;
        }

        .stat-card.danger .stat-value {
            color: #dc3545;
        }

        .stat-card.success .stat-value {
            color: #28a745;
        }

        .stat-label {
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            font-size: 32px;
            color: #e9ecef;
            float: right;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            padding: 15px 20px;
            font-weight: 600;
            color: #333;
        }

        .table-responsive {
            padding: 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table th {
            border-top: none;
            border-bottom: 2px solid #e9ecef;
            color: #667eea;
            font-weight: 600;
            padding: 15px;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .page-title {
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
            }

            .main-content {
                padding: 15px;
            }

            .stat-value {
                font-size: 22px;
            }
        }
    </style>
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
                    <li class="nav-item">
                        <a class="nav-link" href="/profile">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="nav flex-column">
                    <a class="nav-link active" href="/">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a class="nav-link" href="/pos">
                        <i class="fas fa-cash-register"></i> Point of Sale
                    </a>
                    <a class="nav-link" href="/products">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a class="nav-link" href="/suppliers">
                        <i class="fas fa-industry"></i> Suppliers
                    </a>
                    <a class="nav-link" href="/sales">
                        <i class="fas fa-receipt"></i> Sales
                    </a>
                    <a class="nav-link" href="/purchase-orders">
                        <i class="fas fa-shopping-cart"></i> Purchase Orders
                    </a>
                    <a class="nav-link" href="/reports">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <hr class="my-2">
                    <a class="nav-link" href="/admin/users">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a class="nav-link" href="/admin/settings">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="page-title">
                    <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
                    <p class="text-muted">Welcome to your inventory management system</p>
                </div>

                <?php if ($userRole === 'admin'): ?>
                    <!-- Admin Dashboard Content -->
                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <!-- Today's Revenue -->
                        <div class="col-md-3">
                            <div class="stat-card success">
                                <i class="fas fa-money-bill-wave stat-icon"></i>
                                <div class="stat-label">Today's Revenue</div>
                                <div class="stat-value">₱<?= number_format($todaysRevenue, 2) ?></div>
                                <small class="text-muted"><?= $todaysTransactions ?> transactions</small>
                            </div>
                        </div>

                        <!-- Total Products -->
                        <div class="col-md-3">
                            <div class="stat-card">
                                <i class="fas fa-cubes stat-icon"></i>
                                <div class="stat-label">Total Products</div>
                                <div class="stat-value"><?= $totalProducts ?></div>
                                <small class="text-muted"><?= $activeProducts ?> active</small>
                            </div>
                        </div>

                        <!-- Low Stock -->
                        <div class="col-md-3">
                            <div class="stat-card warning">
                                <i class="fas fa-exclamation-triangle stat-icon"></i>
                                <div class="stat-label">Low Stock Products</div>
                                <div class="stat-value"><?= $lowStockProducts ?></div>
                                <small class="text-muted">Need attention</small>
                            </div>
                        </div>

                        <!-- Stock Value -->
                        <div class="col-md-3">
                            <div class="stat-card">
                                <i class="fas fa-warehouse stat-icon"></i>
                                <div class="stat-label">Total Stock Value</div>
                                <div class="stat-value">₱<?= number_format($stockValue, 2) ?></div>
                                <small class="text-muted">Current inventory</small>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row mb-4">
                        <!-- Revenue Chart -->
                        <div class="col-md-8">
                            <div class="chart-container">
                                <h5 class="mb-3">30-Day Sales Trend</h5>
                                <canvas id="salesChart" height="80"></canvas>
                            </div>
                        </div>

                        <!-- Revenue Comparison -->
                        <div class="col-md-4">
                            <div class="chart-container">
                                <h5 class="mb-3">Monthly Comparison</h5>
                                <canvas id="comparisonChart"></canvas>
                                <div class="mt-3">
                                    <small>Last Month: <strong>₱<?= number_format($lastMonthRevenue, 2) ?></strong></small><br>
                                    <small>This Month: <strong class="text-success">₱<?= number_format($thisMonthRevenue, 2) ?></strong></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Sales -->
                        <div class="col-md-7">
                            <div class="table-card">
                                <div class="card-header">
                                    <i class="fas fa-receipt"></i> Recent Sales
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recentSales)): ?>
                                                <?php foreach ($recentSales as $sale): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="/sales/<?= $sale['id'] ?>">
                                                                <?= $sale['invoice_no'] ?>
                                                            </a>
                                                        </td>
                                                        <td><?= $sale['customer_name'] ?></td>
                                                        <td class="fw-bold text-success">₱<?= number_format($sale['total_amount'], 2) ?></td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?= date('H:i A', strtotime($sale['created_at'])) ?>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">
                                                        No sales today
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Alert -->
                        <div class="col-md-5">
                            <div class="table-card">
                                <div class="card-header">
                                    <i class="fas fa-exclamation-circle"></i> Low Stock Alert
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Stock</th>
                                                <th>Min</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($lowStockList)): ?>
                                                <?php foreach ($lowStockList as $product): ?>
                                                    <tr>
                                                        <td><?= $product['name'] ?></td>
                                                        <td>
                                                            <span class="badge bg-danger">
                                                                <?= $product['current_stock'] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= $product['reorder_level'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-4">
                                                        All products at healthy levels
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($userRole === 'teacher'): ?>
                    <!-- Teacher Dashboard Content -->
                    <div class="row mb-4">
                        <!-- Today's Revenue -->
                        <div class="col-md-6">
                            <div class="stat-card success">
                                <i class="fas fa-money-bill-wave stat-icon"></i>
                                <div class="stat-label">Today's Revenue</div>
                                <div class="stat-value">₱<?= number_format($todaysRevenue, 2) ?></div>
                                <small class="text-muted"><?= $todaysTransactions ?> transactions</small>
                            </div>
                        </div>

                        <!-- Recent Sales -->
                        <div class="col-md-6">
                            <div class="table-card">
                                <div class="card-header">
                                    <i class="fas fa-receipt"></i> Recent Sales
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recentSales)): ?>
                                                <?php foreach ($recentSales as $sale): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="/sales/<?= $sale['id'] ?>">
                                                                <?= $sale['invoice_no'] ?>
                                                            </a>
                                                        </td>
                                                        <td><?= $sale['customer_name'] ?></td>
                                                        <td class="fw-bold text-success">₱<?= number_format($sale['total_amount'], 2) ?></td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?= date('H:i A', strtotime($sale['created_at'])) ?>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">
                                                        No sales today
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($userRole === 'student'): ?>
                    <!-- Student Dashboard Content -->
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading">Welcome, Student!</h4>
                        <p>You have access to basic features. Contact your teacher or administrator for more information.</p>
                    </div>

                <?php else: ?>
                    <!-- Default Dashboard Content -->
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading">Access Restricted</h4>
                        <p>Your role is not recognized. Please contact the administrator.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sales Trend Chart
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            fetch('/dashboard/sales-trend?days=30')
                .then(r => r.json())
                .then(d => {
                    if (d.status === 'success') {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: d.data.map(x => x.date),
                                datasets: [{
                                    label: 'Revenue (₱)',
                                    data: d.data.map(x => x.revenue),
                                    borderColor: '#667eea',
                                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }
                });
        }

        // Comparison Chart
        const compCtx = document.getElementById('comparisonChart');
        if (compCtx) {
            new Chart(compCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Last Month', 'This Month'],
                    datasets: [{
                        data: [
                            <?= $lastMonthRevenue ?>,
                            <?= $thisMonthRevenue ?>
                        ],
                        backgroundColor: ['#e9ecef', '#667eea']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
