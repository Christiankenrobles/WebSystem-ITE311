<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================================================
// AUTHENTICATION ROUTES (No filter)
// =====================================================
$routes->get('auth/login', 'AuthController::login');
$routes->post('auth/process-login', 'AuthController::processLogin');
$routes->get('auth/register', 'AuthController::register');
$routes->post('auth/process-register', 'AuthController::processRegister');
$routes->get('auth/logout', 'AuthController::logout');
$routes->get('api/auth/check', 'AuthController::checkAuth');

// Redirect root to dashboard (will be caught by filter)
$routes->get('/', 'DashboardController::index');

// =====================================================
// API ROUTES
// =====================================================

$routes->group('api', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {

    // =====================================================
    // PRODUCT ENDPOINTS (RESTful)
    // =====================================================
    $routes->resource('products', ['controller' => 'ProductController']);
    
    // Additional product endpoints
    $routes->get('products/low-stock', 'ProductController::lowStock');
    $routes->get('products/category/(:num)', 'ProductController::byCategory/$1');
    $routes->get('products/supplier/(:num)', 'ProductController::bySupplier/$1');
    $routes->post('products/bulk-import', 'ProductController::bulkImport');

    // =====================================================
    // SUPPLIER ENDPOINTS (RESTful)
    // =====================================================
    $routes->resource('suppliers', ['controller' => 'SupplierController']);

    // =====================================================
    // PURCHASE ORDER ENDPOINTS (RESTful)
    // =====================================================
    $routes->resource('purchase-orders', ['controller' => 'PurchaseOrderController']);
    
    // Additional PO endpoints
    $routes->post('purchase-orders/(:num)/receive', 'PurchaseOrderController::receive/$1');

    // =====================================================
    // SALE ENDPOINTS (RESTful)
    // =====================================================
    $routes->resource('sales', ['controller' => 'SalesController']);
    
    // Additional sales endpoints
    $routes->get('sales/summary/daily', 'SalesController::summary');

    // =====================================================
    // SALE ITEMS ENDPOINTS (RESTful)
    // =====================================================
    $routes->resource('sale-items', ['controller' => 'SaleItemController']);
    $routes->get('sale-items/sale/(:num)', 'SaleItemController::bySale/$1');

    // =====================================================
    // POS ENDPOINTS
    // =====================================================
    $routes->post('pos/checkout', 'PosController::checkout');
    $routes->get('pos/receipt/(:num)', 'PosController::receipt/$1');

    // =====================================================
    // REPORTS ENDPOINTS
    // =====================================================
    $routes->get('reports/low-stock', 'ReportsController::lowStock');
    $routes->get('reports/daily-sales', 'ReportsController::dailySales');
    $routes->get('reports/monthly-sales', 'ReportsController::monthlySales');
    $routes->get('reports/supplier-analysis', 'ReportsController::supplierAnalysis');

    // =====================================================
    // USER ENDPOINTS
    // =====================================================
    $routes->resource('users', ['controller' => 'UserController']);

});

// =====================================================
// WEB ROUTES
// =====================================================

// Home/Dashboard
$routes->get('/', 'DashboardController::index');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/dashboard/sales-trend', 'DashboardController::salesTrend');
$routes->get('/dashboard/api', 'DashboardController::api');

// Authentication routes
$routes->group('auth', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('login', 'AuthController::login');
});

// =====================================================
// PROTECTED WEB ROUTES (Requires Authentication)
// =====================================================

// Dashboard
$routes->group('', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/sales-trend', 'DashboardController::salesTrend');

    // Product Management (Web)
    $routes->group('products', function($routes) {
    $routes->get('', 'ProductController::index');
    $routes->get('create', 'ProductController::create');
    $routes->post('store', 'ProductController::store');
    $routes->get('(:num)/edit', 'ProductController::edit/$1');
    $routes->post('(:num)/update', 'ProductController::update/$1');
    $routes->get('(:num)', 'ProductController::show/$1');
    $routes->delete('(:num)', 'ProductController::delete/$1');
});

// Supplier Management (Web)
$routes->group('suppliers', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('', 'SupplierController::index');
    $routes->get('create', 'SupplierController::create');
    $routes->post('store', 'SupplierController::store');
    $routes->get('(:num)/edit', 'SupplierController::edit/$1');
    $routes->post('(:num)/update', 'SupplierController::update/$1');
    $routes->get('(:num)', 'SupplierController::show/$1');
    $routes->delete('(:num)', 'SupplierController::delete/$1');
});

// Purchase Orders (Web)
$routes->group('purchase-orders', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('', 'PurchaseOrderController::index');
    $routes->get('create', 'PurchaseOrderController::create');
    $routes->post('store', 'PurchaseOrderController::store');
    $routes->get('(:num)/edit', 'PurchaseOrderController::edit/$1');
    $routes->post('(:num)/update', 'PurchaseOrderController::update/$1');
    $routes->get('(:num)', 'PurchaseOrderController::show/$1');
    $routes->get('(:num)/print', 'PurchaseOrderController::print/$1');
});

// Sales (Web)
$routes->group('sales', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('', 'SalesController::index');
    $routes->get('(:num)', 'SalesController::show/$1');
    $routes->get('(:num)/receipt', 'SalesController::receipt/$1');
});

// Point of Sale (Web)
$routes->group('pos', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('', 'PosController::index');
    $routes->post('add-to-cart', 'PosController::addToCart');
    $routes->get('receipt/(:num)', 'PosController::receipt/$1');
});

// Reports (Web)
$routes->group('reports', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('', 'ReportsController::index');
    $routes->get('low-stock', 'ReportsController::lowStock');
    $routes->get('daily-sales', 'ReportsController::dailySales');
    $routes->get('monthly-sales', 'ReportsController::monthlySales');
    $routes->get('supplier-analysis', 'ReportsController::supplierAnalysis');
});

// User Profile (Web)
$routes->group('profile', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('', 'UserController::profile');
    $routes->post('update', 'UserController::updateProfile');
    $routes->post('change-password', 'UserController::changePassword');
});

// Admin Routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    
    // User Management
    $routes->group('users', function($routes) {
        $routes->get('', 'UserAdminController::index');
        $routes->get('create', 'UserAdminController::create');
        $routes->post('store', 'UserAdminController::store');
        $routes->get('(:num)/edit', 'UserAdminController::edit/$1');
        $routes->post('(:num)/update', 'UserAdminController::update/$1');
        $routes->delete('(:num)', 'UserAdminController::delete/$1');
    });

    // Settings
    $routes->group('settings', function($routes) {
        $routes->get('', 'SettingsController::index');
        $routes->post('update', 'SettingsController::update');
    });
});
}); // End of protected web routes
