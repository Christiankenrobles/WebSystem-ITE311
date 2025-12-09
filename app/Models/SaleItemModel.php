<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemModel extends Model
{
    protected $table      = 'sale_items';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'sale_id' => 'required|integer',
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
     * SaleItem belongs to Sale
     */
    public function sale()
    {
        return $this->belongsTo(SaleModel::class, 'sale_id', 'id');
    }

    /**
     * SaleItem belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id');
    }
}
