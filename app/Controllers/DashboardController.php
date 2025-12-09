<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Display main dashboard
     */
    public function index()
    {
        $saleModel = new SaleModel();
        $productModel = new ProductModel();

        // Get today's sales
        $todaysSales = $saleModel
            ->where('DATE(created_at)', date('Y-m-d'))
            ->findAll();

        // Calculate today's revenue
        $todaysRevenue = array_sum(array_column($todaysSales, 'total_amount'));
        $todaysTransactions = count($todaysSales);

        // Get low stock products
        $lowStockProducts = $productModel->getLowStock();

        // Get total products
        $totalProducts = $productModel->countAll();

        // Get all active products
        $activeProducts = $productModel
            ->where('is_active', 1)
            ->countAllResults();

        // Get current stock value
        $stockValue = $this->db->table('products')
            ->select('SUM(current_stock * cost_price) as total_value')
            ->get()
            ->getRowArray()['total_value'] ?? 0;

        // Get monthly comparison
        $lastMonthRevenue = $this->getMonthlyRevenue(date('Y-m-01', strtotime('-1 month')));
        $thisMonthRevenue = $this->getMonthlyRevenue(date('Y-m-01'));

        // Get recent sales
        $recentSales = $saleModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'todaysRevenue' => round($todaysRevenue, 2),
            'todaysTransactions' => $todaysTransactions,
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'lowStockProducts' => count($lowStockProducts),
            'stockValue' => round($stockValue, 2),
            'lastMonthRevenue' => round($lastMonthRevenue, 2),
            'thisMonthRevenue' => round($thisMonthRevenue, 2),
            'recentSales' => $recentSales,
            'lowStockList' => array_slice($lowStockProducts, 0, 5)
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Get dashboard API data as JSON
     */
    public function api()
    {
        $saleModel = new SaleModel();
        $productModel = new ProductModel();

        // Today's metrics
        $todaysSales = $saleModel
            ->where('DATE(created_at)', date('Y-m-d'))
            ->findAll();

        $todaysRevenue = array_sum(array_column($todaysSales, 'total_amount'));

        // Low stock
        $lowStockProducts = $productModel->getLowStock();

        // Stock value
        $stockValue = $this->db->table('products')
            ->select('SUM(current_stock * cost_price) as total_value')
            ->get()
            ->getRowArray()['total_value'] ?? 0;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'today' => [
                    'revenue' => round($todaysRevenue, 2),
                    'transactions' => count($todaysSales)
                ],
                'inventory' => [
                    'total_products' => $productModel->countAll(),
                    'active_products' => $productModel->where('is_active', 1)->countAllResults(),
                    'low_stock_count' => count($lowStockProducts),
                    'total_stock_value' => round($stockValue, 2)
                ]
            ]
        ]);
    }

    /**
     * Get sales trend data
     */
    public function salesTrend()
    {
        $days = intval($_GET['days'] ?? 30);
        $saleModel = new SaleModel();

        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $sales = $saleModel
                ->where('DATE(created_at)', $date)
                ->findAll();

            $revenue = array_sum(array_column($sales, 'total_amount'));
            $data[] = [
                'date' => $date,
                'revenue' => round($revenue, 2),
                'transactions' => count($sales)
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Helper: Get monthly revenue
     */
    private function getMonthlyRevenue($monthStart)
    {
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $saleModel = new SaleModel();

        $sales = $saleModel
            ->where('created_at >=', $monthStart . ' 00:00:00')
            ->where('created_at <=', $monthEnd . ' 23:59:59')
            ->findAll();

        return array_sum(array_column($sales, 'total_amount'));
    }
}
