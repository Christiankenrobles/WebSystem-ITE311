<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleItemModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class SalesController extends ResourceController
{
    protected $modelName = 'App\Models\SaleModel';
    protected $format = 'json';

    /**
     * List all sales with filters
     * 
     * GET /api/sales?page=1&per_page=20&date_from=2025-01-01&date_to=2025-12-31&invoice=INV-20251205-ABC123
     * 
     * @return ResponseInterface
     */
    public function index()
    {
        $page = intval($_GET['page'] ?? 1);
        $perPage = intval($_GET['per_page'] ?? 20);
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $invoice = $_GET['invoice'] ?? null;
        $customerId = $_GET['customer_id'] ?? null;

        $model = $this->model;

        // Apply filters
        if ($dateFrom) {
            $model = $model->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $model = $model->where('DATE(created_at) <=', $dateTo);
        }

        if ($invoice) {
            $model = $model->like('invoice_no', $invoice);
        }

        if ($customerId) {
            $model = $model->where('customer_id', $customerId);
        }

        // Get total count
        $total = $model->countAllResults(false);

        // Get paginated data
        $sales = $model
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage)
            ->getArray();

        $pager = $this->model->pager;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $sales,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Get sale details including sale items
     * 
     * GET /api/sales/:id
     * 
     * @param  mixed  $id
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $sale = $this->model->find($id);

        if (!$sale) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sale not found',
                'error' => 'not_found'
            ])->setStatusCode(404);
        }

        // Get sale items
        $saleItemModel = new SaleItemModel();
        $items = $saleItemModel
            ->where('sale_id', $id)
            ->findAll();

        // Calculate summary
        $itemCount = count($items);
        $totalQuantity = array_sum(array_column($items, 'quantity'));

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'sale' => $sale,
                'items' => $items,
                'summary' => [
                    'items_count' => $itemCount,
                    'total_quantity' => $totalQuantity,
                    'total_amount' => $sale['total_amount'],
                    'paid_amount' => $sale['paid_amount'],
                    'change_amount' => $sale['change_amount']
                ]
            ]
        ]);
    }

    /**
     * Get daily sales summary
     * 
     * GET /api/sales/summary/daily?date=2025-12-05
     */
    public function summary()
    {
        $date = $_GET['date'] ?? date('Y-m-d');

        $sales = $this->model
            ->where('DATE(created_at)', $date)
            ->findAll();

        if (empty($sales)) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'date' => $date,
                    'total_sales' => 0,
                    'total_revenue' => 0,
                    'transaction_count' => 0,
                    'average_transaction' => 0,
                    'sales' => []
                ]
            ]);
        }

        $totalRevenue = array_sum(array_column($sales, 'total_amount'));
        $transactionCount = count($sales);
        $averageTransaction = $totalRevenue / $transactionCount;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'total_sales' => $totalRevenue,
                'total_revenue' => $totalRevenue,
                'transaction_count' => $transactionCount,
                'average_transaction' => round($averageTransaction, 2),
                'sales' => $sales
            ]
        ]);
    }
}
