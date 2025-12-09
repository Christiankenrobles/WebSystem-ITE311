<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleItemModel;
use CodeIgniter\RESTful\ResourceController;

class SaleController extends ResourceController
{
    protected $modelName = 'App\Models\SaleModel';
    protected $format    = 'json';

    public function index()
    {
        $data['sales'] = $this->model->findAll();
        return view('sales/index', $data);
    }

    public function show($id = null)
    {
        $saleItemModel = new SaleItemModel();
        $data['sale'] = $this->model->find($id);
        $data['items'] = $saleItemModel->where('sale_id', $id)->findAll();
        return view('sales/show', $data);
    }

    public function items($id = null)
    {
        $saleItemModel = new SaleItemModel();
        $data['items'] = $saleItemModel->where('sale_id', $id)->findAll();
        return view('sales/items', $data);
    }
}
