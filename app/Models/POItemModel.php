<?php

namespace App\Models;

use CodeIgniter\Model;

class POItemModel extends Model
{
    protected $table      = 'po_items';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'received_quantity'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'po_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|integer|greater_than[0]',
        'unit_price' => 'required|decimal|greater_than[0]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * POItem belongs to PurchaseOrder
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrderModel::class, 'po_id', 'id');
    }

    /**
     * POItem belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id');
    }
}
