# Supplies Inventory and Sales System - Complete Folder Structure

## Project Overview
A comprehensive inventory management and point-of-sale system built with CodeIgniter 4 featuring multiple modules for product management, suppliers, sales tracking, and reporting.

---

## 📁 Complete Folder Structure

```
FISHING/
├── app/
│   ├── Controllers/
│   │   ├── BaseController.php                    ✅ (exists)
│   │   ├── Home.php                              ✅ (exists)
│   │   ├── AuthController.php                    📝 NEW
│   │   ├── DashboardController.php               📝 NEW
│   │   ├── ProductController.php                 ✅ (exists)
│   │   ├── SupplierController.php                ✅ (exists)
│   │   ├── PurchaseOrderController.php           ✅ (exists)
│   │   ├── SaleController.php                    ✅ (exists)
│   │   ├── SaleItemController.php                📝 NEW
│   │   ├── PosController.php                     ✅ (exists)
│   │   ├── UserController.php                    ✅ (exists)
│   │   ├── ReportController.php                  📝 NEW
│   │   ├── API/                                  📝 NEW
│   │   │   ├── ProductApiController.php
│   │   │   ├── SaleApiController.php
│   │   │   ├── SupplierApiController.php
│   │   │   └── ReportApiController.php
│   │   └── Admin/                                📝 NEW
│   │       ├── UserAdminController.php
│   │       └── SettingsController.php
│   │
│   ├── Models/
│   │   ├── ProductModel.php                      ✅ (exists)
│   │   ├── SaleModel.php                         ✅ (exists)
│   │   ├── UserModel.php                         ✅ (exists)
│   │   ├── SupplierModel.php                     📝 NEW
│   │   ├── PurchaseOrderModel.php                📝 NEW
│   │   ├── SaleItemModel.php                     📝 NEW
│   │   ├── StockModel.php                        📝 NEW
│   │   ├── CategoryModel.php                     📝 NEW
│   │   ├── CategorySupplierModel.php             📝 NEW
│   │   └── ReportModel.php                       📝 NEW
│   │
│   ├── Database/
│   │   ├── Migrations/
│   │   │   ├── 2025-12-05-000001_CreateUsersTable.php
│   │   │   ├── 2025-12-05-000002_CreateCategoriesTable.php
│   │   │   ├── 2025-12-05-000003_CreateSuppliersTable.php
│   │   │   ├── 2025-12-05-000004_CreateProductsTable.php
│   │   │   ├── 2025-12-05-000005_CreateStockTable.php
│   │   │   ├── 2025-12-05-000006_CreatePurchaseOrdersTable.php
│   │   │   ├── 2025-12-05-000007_CreatePurchaseOrderItemsTable.php
│   │   │   ├── 2025-12-05-000008_CreateSalesTable.php
│   │   │   ├── 2025-12-05-000009_CreateSaleItemsTable.php
│   │   │   └── 2025-12-05-000010_CreateAuditLogsTable.php
│   │   └── Seeds/
│   │       ├── DatabaseSeeder.php
│   │       ├── UserSeeder.php
│   │       ├── CategorySeeder.php
│   │       ├── SupplierSeeder.php
│   │       └── ProductSeeder.php
│   │
│   ├── Views/
│   │   ├── welcome_message.php                   ✅ (exists)
│   │   ├── layout/
│   │   │   ├── main.php                          📝 NEW
│   │   │   ├── header.php                        📝 NEW
│   │   │   ├── sidebar.php                       📝 NEW
│   │   │   └── footer.php                        📝 NEW
│   │   ├── auth/                                 ✅ (exists - folder)
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   └── forgot_password.php
│   │   ├── dashboard/                            📝 NEW
│   │   │   ├── index.php
│   │   │   ├── sales_summary.php
│   │   │   ├── inventory_status.php
│   │   │   └── quick_stats.php
│   │   ├── products/                             ✅ (exists - folder)
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   ├── show.php
│   │   │   └── import.php
│   │   ├── suppliers/                            ✅ (exists - folder)
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── show.php
│   │   ├── purchase_orders/                      ✅ (exists - folder)
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   ├── show.php
│   │   │   └── print.php
│   │   ├── sales/                                📝 NEW
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   ├── show.php
│   │   │   └── receipt.php
│   │   ├── pos/                                  ✅ (exists - folder)
│   │   │   ├── index.php
│   │   │   ├── checkout.php
│   │   │   ├── cart.php
│   │   │   ├── payment.php
│   │   │   └── receipt.php
│   │   ├── reports/                              ✅ (exists - folder)
│   │   │   ├── index.php
│   │   │   ├── low_stock.php
│   │   │   ├── daily_sales.php
│   │   │   ├── monthly_sales.php
│   │   │   ├── supplier_analysis.php
│   │   │   ├── inventory_valuation.php
│   │   │   └── export.php
│   │   ├── users/                                ✅ (exists - folder)
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── profile.php
│   │   ├── components/                           ✅ (exists - folder)
│   │   │   ├── navbar.php
│   │   │   ├── sidebar_menu.php
│   │   │   ├── product_table.php
│   │   │   ├── sale_form.php
│   │   │   ├── modal_confirm.php
│   │   │   └── alert.php
│   │   ├── errors/                               ✅ (exists - folder)
│   │   │   ├── html/
│   │   │   │   ├── error_404.php
│   │   │   │   ├── error_500.php
│   │   │   │   └── error_403.php
│   │   │   └── cli/
│   │   └── index.html                            ✅ (exists)
│   │
│   ├── Filters/
│   │   ├── AuthFilter.php                        📝 NEW
│   │   ├── AdminFilter.php                       📝 NEW
│   │   └── RoleFilter.php                        📝 NEW
│   │
│   ├── Helpers/
│   │   ├── common_helper.php                     📝 NEW
│   │   ├── format_helper.php                     📝 NEW
│   │   ├── auth_helper.php                       📝 NEW
│   │   ├── report_helper.php                     📝 NEW
│   │   └── notification_helper.php               📝 NEW
│   │
│   ├── Entities/                                 📝 NEW
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Supplier.php
│   │   ├── Sale.php
│   │   └── PurchaseOrder.php
│   │
│   ├── Libraries/                                📝 NEW
│   │   ├── PosService.php
│   │   ├── ReportGenerator.php
│   │   ├── StockManager.php
│   │   ├── PaymentProcessor.php
│   │   └── NotificationService.php
│   │
│   ├── Services/                                 📝 NEW
│   │   ├── SaleService.php
│   │   ├── InventoryService.php
│   │   ├── EmailService.php
│   │   └── AuthService.php
│   │
│   ├── Config/
│   │   ├── Routes.php                            ✅ (exists)
│   │   ├── Filters.php                           ✅ (exists)
│   │   ├── AuthConfig.php                        📝 NEW
│   │   ├── PosConfig.php                         📝 NEW
│   │   ├── ReportConfig.php                      📝 NEW
│   │   └── Boot/                                 ✅ (exists)
│   │
│   ├── Language/
│   │   ├── en/
│   │   │   ├── Common.php
│   │   │   ├── Messages.php                      📝 NEW
│   │   │   ├── Validation.php
│   │   │   └── Reports.php                       📝 NEW
│   │
│   ├── Validation/                               📝 NEW
│   │   ├── ProductRules.php
│   │   ├── SaleRules.php
│   │   ├── SupplierRules.php
│   │   ├── UserRules.php
│   │   └── PurchaseOrderRules.php
│   │
│   ├── Events/                                   📝 NEW
│   │   ├── SaleCompleted.php
│   │   ├── LowStockAlert.php
│   │   └── PurchaseOrderReceived.php
│   │
│   ├── Common.php                                ✅ (exists)
│   └── index.html                                ✅ (exists)
│
├── public/
│   ├── index.php
│   ├── robots.txt                                ✅ (exists)
│   ├── css/                                      📝 NEW
│   │   ├── bootstrap.min.css
│   │   ├── style.css
│   │   ├── pos.css
│   │   ├── dashboard.css
│   │   ├── reports.css
│   │   └── theme.css
│   ├── js/
│   │   ├── jquery-3.6.0.min.js                   📝 NEW
│   │   ├── bootstrap.min.js                      📝 NEW
│   │   ├── app.js
│   │   ├── pos.js
│   │   ├── cart.js
│   │   ├── charts.js                             📝 NEW (Chart.js)
│   │   ├── datatables.js
│   │   └── validation.js
│   ├── images/
│   │   ├── logo.png                              📝 NEW
│   │   ├── icons/
│   │   └── uploads/
│   └── uploads/                                  📝 NEW
│       ├── products/
│       ├── suppliers/
│       └── temp/
│
├── system/                                       ✅ (exists - CodeIgniter core)
├── tests/                                        ✅ (exists)
│   ├── unit/
│   │   ├── ProductModelTest.php                  📝 NEW
│   │   ├── SaleServiceTest.php                   📝 NEW
│   │   └── InventoryServiceTest.php              📝 NEW
│   ├── database/
│   └── _support/
│
├── writable/                                     ✅ (exists)
│   ├── cache/
│   ├── debugbar/
│   ├── logs/
│   ├── session/
│   └── uploads/
│
├── .env                                          ✅ (exists - config file)
├── composer.json                                 ✅ (exists)
├── phpunit.xml.dist                              ✅ (exists)
├── spark                                         ✅ (exists - CLI tool)
├── README.md                                     ✅ (exists)
├── STRUCTURE_OUTLINE.md                          📝 THIS FILE
└── API_ROUTES.md                                 📝 NEW (see below)
```

---

## 🛣️ Routes Configuration

### **Routes/api.php** (NEW - API Endpoints)

```php
<?php
// API Routes for RESTful endpoints
$routes->group('api', ['namespace' => 'App\Controllers\API'], function($routes) {
    // Product API endpoints
    $routes->resource('products', ['controller' => 'ProductApiController']);
    $routes->get('products/low-stock', 'ProductApiController::lowStock');
    $routes->get('products/by-category/(:num)', 'ProductApiController::byCategory/$1');
    
    // Supplier API endpoints
    $routes->resource('suppliers', ['controller' => 'SupplierApiController']);
    
    // Sales API endpoints
    $routes->resource('sales', ['controller' => 'SaleApiController']);
    $routes->post('sales/quick-add', 'SaleApiController::quickAdd');
    $routes->get('sales/daily', 'SaleApiController::dailySales');
    
    // Reports API endpoints
    $routes->get('reports/low-stock', 'ReportApiController::lowStock');
    $routes->get('reports/daily-sales', 'ReportApiController::dailySales');
    $routes->get('reports/monthly-sales', 'ReportApiController::monthlySales');
    $routes->get('reports/supplier-analysis', 'ReportApiController::supplierAnalysis');
});
```

### **Routes/web.php** (NEW - Web Routes)

```php
<?php
// Web Routes
$routes = new CodeIgniter\Router\RouteCollection();

// Dashboard
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

// Authentication
$routes->group('auth', function($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::attemptRegister');
    $routes->post('logout', 'AuthController::logout');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('reset-password', 'AuthController::resetPassword');
});

// Product Management
$routes->group('products', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'ProductController::index');
    $routes->get('create', 'ProductController::create');
    $routes->post('store', 'ProductController::store');
    $routes->get('(:num)/edit', 'ProductController::edit/$1');
    $routes->post('(:num)/update', 'ProductController::update/$1');
    $routes->get('(:num)', 'ProductController::show/$1');
    $routes->delete('(:num)', 'ProductController::delete/$1');
    $routes->post('bulk-import', 'ProductController::bulkImport');
});

// Supplier Management
$routes->group('suppliers', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'SupplierController::index');
    $routes->get('create', 'SupplierController::create');
    $routes->post('store', 'SupplierController::store');
    $routes->get('(:num)/edit', 'SupplierController::edit/$1');
    $routes->post('(:num)/update', 'SupplierController::update/$1');
    $routes->get('(:num)', 'SupplierController::show/$1');
    $routes->delete('(:num)', 'SupplierController::delete/$1');
});

// Purchase Orders
$routes->group('purchase-orders', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'PurchaseOrderController::index');
    $routes->get('create', 'PurchaseOrderController::create');
    $routes->post('store', 'PurchaseOrderController::store');
    $routes->get('(:num)/edit', 'PurchaseOrderController::edit/$1');
    $routes->post('(:num)/update', 'PurchaseOrderController::update/$1');
    $routes->get('(:num)', 'PurchaseOrderController::show/$1');
    $routes->post('(:num)/receive', 'PurchaseOrderController::receive/$1');
    $routes->get('(:num)/print', 'PurchaseOrderController::print/$1');
});

// Sales Management
$routes->group('sales', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'SaleController::index');
    $routes->get('create', 'SaleController::create');
    $routes->post('store', 'SaleController::store');
    $routes->get('(:num)', 'SaleController::show/$1');
    $routes->get('(:num)/receipt', 'SaleController::receipt/$1');
    $routes->get('(:num)/print', 'SaleController::print/$1');
});

// Point of Sale (POS)
$routes->group('pos', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'PosController::index');
    $routes->post('add-to-cart', 'PosController::addToCart');
    $routes->post('checkout', 'PosController::checkout');
    $routes->get('receipt/(:num)', 'PosController::receipt/$1');
});

// Reports
$routes->group('reports', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'ReportController::index');
    $routes->get('low-stock', 'ReportController::lowStock');
    $routes->get('daily-sales', 'ReportController::dailySales');
    $routes->get('monthly-sales', 'ReportController::monthlySales');
    $routes->get('supplier-analysis', 'ReportController::supplierAnalysis');
    $routes->get('inventory-valuation', 'ReportController::inventoryValuation');
    $routes->post('export/(:any)', 'ReportController::export/$1');
});

// User Management (Admin only)
$routes->group('admin', ['filter' => 'auth|admin'], function($routes) {
    $routes->group('users', function($routes) {
        $routes->get('', 'Admin\UserAdminController::index');
        $routes->get('create', 'Admin\UserAdminController::create');
        $routes->post('store', 'Admin\UserAdminController::store');
        $routes->get('(:num)/edit', 'Admin\UserAdminController::edit/$1');
        $routes->post('(:num)/update', 'Admin\UserAdminController::update/$1');
        $routes->delete('(:num)', 'Admin\UserAdminController::delete/$1');
    });
    
    $routes->group('settings', function($routes) {
        $routes->get('', 'Admin\SettingsController::index');
        $routes->post('update', 'Admin\SettingsController::update');
    });
});

// User Profile
$routes->group('profile', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'UserController::profile');
    $routes->post('update', 'UserController::updateProfile');
    $routes->post('change-password', 'UserController::changePassword');
});
```

---

## 📊 Database Migrations Overview

### Migration Files (in order of execution):

1. **CreateUsersTable** - User management (admin, staff)
2. **CreateCategoriesTable** - Product categories
3. **CreateSuppliersTable** - Supplier information
4. **CreateProductsTable** - Product catalog
5. **CreateStockTable** - Stock tracking and adjustments
6. **CreatePurchaseOrdersTable** - Purchase orders header
7. **CreatePurchaseOrderItemsTable** - Purchase order line items
8. **CreateSalesTable** - Sales transactions
9. **CreateSaleItemsTable** - Sale line items
10. **CreateAuditLogsTable** - Audit trail

---

## 🗂️ Models Overview

### Core Models:
- `ProductModel` - Product CRUD operations
- `SupplierModel` - Supplier management
- `UserModel` - User authentication & profile
- `SaleModel` - Sales transactions
- `SaleItemModel` - Individual sale items
- `PurchaseOrderModel` - Purchase orders
- `StockModel` - Inventory tracking
- `CategoryModel` - Product categories
- `ReportModel` - Report data aggregation

---

## 🎮 Controllers Overview

### Web Controllers:
- `AuthController` - Login, register, password reset
- `DashboardController` - Admin dashboard
- `ProductController` - Product management (CRUD)
- `SupplierController` - Supplier management (CRUD)
- `PurchaseOrderController` - PO management & receiving
- `SaleController` - Sales record management
- `PosController` - Point of sale operations
- `UserController` - User profile management
- `ReportController` - Report generation & export
- `Admin/UserAdminController` - Admin user management
- `Admin/SettingsController` - System settings

### API Controllers (in `Controllers/API/`):
- `ProductApiController` - Product endpoints
- `SupplierApiController` - Supplier endpoints
- `SaleApiController` - Sales endpoints
- `ReportApiController` - Report data endpoints

---

## 👁️ Views Structure

### Folder Breakdown:

```
Views/
├── layout/                  - Master layouts & templates
├── auth/                    - Login, register pages
├── dashboard/               - Dashboard & analytics
├── products/                - Product management pages
├── suppliers/               - Supplier management pages
├── purchase_orders/         - Purchase order pages
├── sales/                   - Sales record pages
├── pos/                     - Point of sale interface
├── reports/                 - Various reports pages
├── users/                   - User management pages
├── components/              - Reusable UI components
└── errors/                  - Error pages (404, 500, etc.)
```

---

## 🔐 Security & Filtering

### Filters:
- `AuthFilter` - Check if user is authenticated
- `AdminFilter` - Check if user is admin
- `RoleFilter` - Check specific user roles

### Key Security Features:
- CSRF protection on all forms
- Input validation via Validation rules
- Password hashing for users
- Session management
- Role-based access control (RBAC)

---

## 📦 Dependencies & Packages

Recommended composer packages:
```json
{
  "require": {
    "codeigniter4/framework": "^4.4",
    "phpmailer/phpmailer": "^6.8",
    "fpdf/fpdf": "^1.86",
    "phpoffice/phpspreadsheet": "^1.29"
  }
}
```

---

## 🚀 Key Implementation Notes

### 1. **Database Transactions**
   - Sales and purchase orders should use transactions
   - Stock updates must be atomic

### 2. **Stock Management**
   - Track stock levels per product
   - Alert on low stock
   - Prevent negative stock

### 3. **POS System**
   - Session-based cart
   - Real-time product search
   - Multiple payment methods

### 4. **Reporting**
   - Low stock reports
   - Daily/Monthly sales analysis
   - Supplier performance analysis
   - Inventory valuation

### 5. **Audit Trail**
   - Log all critical operations
   - Track user actions
   - Maintain data history

---

## 📝 Next Steps

1. Create migration files for database schema
2. Generate Models with appropriate relationships
3. Implement Controllers with business logic
4. Create Views with Bootstrap UI
5. Set up API routes for frontend requests
6. Implement authentication & authorization
7. Add validation rules
8. Create seeders for test data
9. Develop POS interface
10. Build reporting functionality

---

**Status**: 🔄 Ready for implementation
**Last Updated**: December 5, 2025
