<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sku', 
        'name', 
        'description',
        'category_id', 
        'brand', 
        'supplier_id', 
        'cost_price', 
        'sell_price', 
        'current_stock', 
        'reorder_level',
        'unit',
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
        'sku' => 'required|is_unique[products.sku,id,{id}]',
        'name' => 'required',
        'supplier_id' => 'required|integer',
        'cost_price' => 'required|decimal',
        'sell_price' => 'required|decimal',
        'current_stock' => 'required|integer',
        'reorder_level' => 'required|integer',
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
     * Product belongs to Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'id');
    }

    /**
     * Product has many SaleItems
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItemModel::class, 'product_id', 'id');
    }

    /**
     * Product has many POItems
     */
    public function poItems()
    {
        return $this->hasMany(POItemModel::class, 'product_id', 'id');
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get low stock products
     */
    public function getLowStock($threshold = null)
    {
        $query = $this->asArray()
            ->where('current_stock <=', $threshold ?? 'reorder_level');
        
        return $query->findAll();
    }

    /**
     * Update stock
     */
    public function updateStock($productId, $quantity, $operation = 'decrease')
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return false;
        }

        $currentStock = $product['current_stock'];
        $newStock = $operation === 'decrease' 
            ? $currentStock - $quantity 
            : $currentStock + $quantity;

        if ($newStock < 0) {
            return false;
        }

        return $this->update($productId, ['current_stock' => $newStock]);
    }

    /**
     * Check stock availability
     */
    public function hasStock($productId, $quantity)
    {
        $product = $this->find($productId);
        return $product && $product['current_stock'] >= $quantity;
    }
}
