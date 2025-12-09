<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table      = 'purchase_orders';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_delivery_date',
        'received_date',
        'total_amount',
        'status',
        'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'po_number' => 'required|is_unique[purchase_orders.po_number,id,{id}]',
        'supplier_id' => 'required|integer',
        'total_amount' => 'required|decimal',
        'status' => 'required',
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
     * PurchaseOrder belongs to Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'id');
    }

    /**
     * PurchaseOrder has many POItems
     */
    public function poItems()
    {
        return $this->hasMany(POItemModel::class, 'po_id', 'id');
    }
}
