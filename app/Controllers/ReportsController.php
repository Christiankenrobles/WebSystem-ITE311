<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ReportsController extends ResourceController
{
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Get low stock products report
     * 
     * GET /api/reports/low-stock?threshold=10&limit=50
     * 
     * Returns products where current_stock <= reorder_level or threshold
     * 
     * @return ResponseInterface
     */
    public function lowStock()
    {
        $threshold = intval($_GET['threshold'] ?? null);
        $limit = intval($_GET['limit'] ?? 50);

        $productModel = new ProductModel();

        if ($threshold) {
            // Use provided threshold
            $products = $productModel
                ->where('current_stock <=', $threshold)
                ->where('is_active', 1)
                ->limit($limit)
                ->findAll();
        } else {
            // Use reorder_level from each product
            $products = $productModel
                ->selectRaw('id, name, sku, current_stock, reorder_level, unit, supplier_id')
                ->where('current_stock <= reorder_level')
                ->where('is_active', 1)
                ->limit($limit)
                ->findAll();
        }

        $count = count($products);
        $totalValue = 0;

        // Calculate total stock value and status
        foreach ($products as &$product) {
            $product['status'] = $product['current_stock'] == 0 ? 'out_of_stock' : 'low_stock';
            $product['variance'] = $product['reorder_level'] - $product['current_stock'];
            // Assuming cost_price is available
            $product['stock_value'] = $product['current_stock'] * ($product['cost_price'] ?? 0);
            $totalValue += $product['stock_value'];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'report_date' => date('Y-m-d H:i:s'),
                'products_count' => $count,
                'total_stock_value' => round($totalValue, 2),
                'products' => $products
            ]
        ]);
    }

    /**
     * Get daily sales report
     * 
     * GET /api/reports/daily-sales?date=2025-12-05
     * 
     * Returns sales and items sold for a specific date
     * 
     * @return ResponseInterface
     */
    public function dailySales()
    {
        $date = $_GET['date'] ?? date('Y-m-d');

        $saleModel = new SaleModel();

        // Validate date format
        if (!strtotime($date)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid date format. Use YYYY-MM-DD',
                'error' => 'invalid_date'
            ])->setStatusCode(422);
        }

        // Get sales for the day
        $sales = $saleModel
            ->where('DATE(created_at)', $date)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        if (empty($sales)) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'date' => $date,
                    'total_transactions' => 0,
                    'total_revenue' => 0,
                    'total_items_sold' => 0,
                    'average_transaction_value' => 0,
                    'payment_methods' => [],
                    'sales' => []
                ]
            ]);
        }

        // Calculate metrics
        $totalRevenue = 0;
        $totalItemsSold = 0;
        $paymentMethods = [];

        // Get sale items for the day
        $saleIds = array_column($sales, 'id');
        $query = $this->db->table('sale_items')
            ->whereIn('sale_id', $saleIds)
            ->get()
            ->getResultArray();

        $totalItemsSold = count($query);

        foreach ($sales as $sale) {
            $totalRevenue += $sale['total_amount'];

            $method = $sale['payment_method'] ?? 'unknown';
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'amount' => 0];
            }
            $paymentMethods[$method]['count']++;
            $paymentMethods[$method]['amount'] += $sale['total_amount'];
        }

        $transactionCount = count($sales);
        $averageTransactionValue = $transactionCount > 0 
            ? round($totalRevenue / $transactionCount, 2) 
            : 0;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'total_transactions' => $transactionCount,
                'total_revenue' => round($totalRevenue, 2),
                'total_items_sold' => $totalItemsSold,
                'average_transaction_value' => $averageTransactionValue,
                'payment_methods' => $paymentMethods,
                'sales' => $sales
            ]
        ]);
    }

    /**
     * Get monthly sales report
     * 
     * GET /api/reports/monthly-sales?year=2025&month=12
     * 
     * @return ResponseInterface
     */
    public function monthlySales()
    {
        $year = intval($_GET['year'] ?? date('Y'));
        $month = intval($_GET['month'] ?? date('m'));

        if ($month < 1 || $month > 12) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Month must be between 1 and 12',
                'error' => 'invalid_month'
            ])->setStatusCode(422);
        }

        $saleModel = new SaleModel();
        $startDate = date('Y-m-01', strtotime("{$year}-{$month}-01"));
        $endDate = date('Y-m-t', strtotime("{$year}-{$month}-01"));

        $sales = $saleModel
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->findAll();

        if (empty($sales)) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'year' => $year,
                    'month' => $month,
                    'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                    'total_revenue' => 0,
                    'total_transactions' => 0,
                    'daily_breakdown' => []
                ]
            ]);
        }

        // Group by day
        $dailyBreakdown = [];
        foreach ($sales as $sale) {
            $day = date('d', strtotime($sale['created_at']));
            if (!isset($dailyBreakdown[$day])) {
                $dailyBreakdown[$day] = ['transactions' => 0, 'revenue' => 0];
            }
            $dailyBreakdown[$day]['transactions']++;
            $dailyBreakdown[$day]['revenue'] += $sale['total_amount'];
        }

        $totalRevenue = array_sum(array_column($sales, 'total_amount'));
        $transactionCount = count($sales);

        // Format daily breakdown
        $formattedDailyBreakdown = [];
        for ($day = 1; $day <= intval(date('t', strtotime($startDate))); $day++) {
            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
            if (isset($dailyBreakdown[$dayStr])) {
                $formattedDailyBreakdown[] = [
                    'day' => $dayStr,
                    'date' => "{$year}-{$month}-{$dayStr}",
                    'transactions' => $dailyBreakdown[$dayStr]['transactions'],
                    'revenue' => round($dailyBreakdown[$dayStr]['revenue'], 2)
                ];
            } else {
                $formattedDailyBreakdown[] = [
                    'day' => $dayStr,
                    'date' => "{$year}-{$month}-{$dayStr}",
                    'transactions' => 0,
                    'revenue' => 0
                ];
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'year' => $year,
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'total_revenue' => round($totalRevenue, 2),
                'total_transactions' => $transactionCount,
                'average_daily_revenue' => round($totalRevenue / 30, 2),
                'daily_breakdown' => $formattedDailyBreakdown
            ]
        ]);
    }

    /**
     * Get supplier analysis report
     * 
     * GET /api/reports/supplier-analysis?supplier_id=1&period=monthly
     * 
     * @return ResponseInterface
     */
    public function supplierAnalysis()
    {
        $supplierId = intval($_GET['supplier_id'] ?? 0);
        $period = $_GET['period'] ?? 'monthly'; // daily, weekly, monthly

        if (!$supplierId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'supplier_id is required',
                'error' => 'missing_supplier_id'
            ])->setStatusCode(422);
        }

        $query = $this->db->table('products')
            ->where('supplier_id', $supplierId)
            ->select('COUNT(*) as total_products, SUM(current_stock) as total_stock')
            ->get()
            ->getRowArray();

        if (!$query || $query['total_products'] == 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier not found or has no products',
                'error' => 'not_found'
            ])->setStatusCode(404);
        }

        // Get purchase order data
        $poQuery = $this->db->table('purchase_orders')
            ->where('supplier_id', $supplierId)
            ->select('COUNT(*) as total_orders, SUM(total_amount) as total_spent')
            ->get()
            ->getRowArray();

        $totalOrders = $poQuery['total_orders'] ?? 0;
        $totalSpent = $poQuery['total_spent'] ?? 0;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'supplier_id' => $supplierId,
                'total_products' => intval($query['total_products']),
                'total_stock' => intval($query['total_stock'] ?? 0),
                'total_purchase_orders' => $totalOrders,
                'total_spent' => round($totalSpent, 2),
                'average_order_value' => $totalOrders > 0 ? round($totalSpent / $totalOrders, 2) : 0,
                'analysis_period' => $period
            ]
        ]);
    }
}
