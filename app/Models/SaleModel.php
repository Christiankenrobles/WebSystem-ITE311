<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table      = 'sales';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_no', 
        'user_id', 
        'customer_name', 
        'total_amount', 
        'paid_amount', 
        'change_amount', 
        'payment_method',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'invoice_no' => 'required|is_unique[sales.invoice_no,id,{id}]',
        'user_id' => 'required|integer',
        'customer_name' => 'required',
        'total_amount' => 'required|decimal',
        'paid_amount' => 'required|decimal',
        'change_amount' => 'required|decimal',
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
     * Sale has many SaleItems
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItemModel::class, 'sale_id', 'id');
    }

    /**
     * Sale belongs to User
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}
