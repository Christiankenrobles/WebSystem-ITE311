<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table      = 'suppliers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'contact_person',
        'payment_terms',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|is_unique[suppliers.name,id,{id}]',
        'email' => 'required|valid_email',
        'phone' => 'required',
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
     * Supplier has many Products
     */
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'supplier_id', 'id');
    }

    /**
     * Supplier has many PurchaseOrders
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrderModel::class, 'supplier_id', 'id');
    }
}
