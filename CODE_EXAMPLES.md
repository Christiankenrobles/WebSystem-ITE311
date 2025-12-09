# Code Examples & Quick Reference

## 🔌 API Usage Examples

### 1. POS Checkout Example

**Request**:
```bash
curl -X POST http://localhost:8080/api/pos/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 5, "unit_price": 250.00},
      {"product_id": 2, "quantity": 3, "unit_price": 150.00}
    ],
    "customer_name": "Maria Santos",
    "payment_method": "cash",
    "paid_amount": 2000.00
  }'
```

**Response (Success)**:
```json
{
  "status": "success",
  "message": "Sale completed successfully",
  "data": {
    "sale_id": 42,
    "invoice_no": "INV-20251205-7F9E2B",
    "customer_name": "Maria Santos",
    "total_amount": 1700.00,
    "paid_amount": 2000.00,
    "change_amount": 300.00,
    "payment_method": "cash",
    "items_count": 2,
    "created_at": "2025-12-05 15:45:30"
  }
}
```

### 2. Get Sales List with Filters

**Request**:
```bash
curl "http://localhost:8080/api/sales?date_from=2025-12-01&date_to=2025-12-05&page=1&per_page=10"
```

**Response**:
```json
{
  "status": "success",
  "data": [
    {
      "id": 42,
      "invoice_no": "INV-20251205-7F9E2B",
      "customer_name": "Maria Santos",
      "total_amount": 1700.00,
      "payment_method": "cash",
      "created_at": "2025-12-05 15:45:30"
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 42,
    "total_pages": 5
  }
}
```

### 3. Get Sale Details with Items

**Request**:
```bash
curl http://localhost:8080/api/sales/42
```

**Response**:
```json
{
  "status": "success",
  "data": {
    "sale": {
      "id": 42,
      "invoice_no": "INV-20251205-7F9E2B",
      "customer_name": "Maria Santos",
      "total_amount": 1700.00,
      "paid_amount": 2000.00,
      "change_amount": 300.00,
      "created_at": "2025-12-05 15:45:30"
    },
    "items": [
      {
        "id": 1,
        "sale_id": 42,
        "product_id": 1,
        "quantity": 5,
        "unit_price": 250.00,
        "total_price": 1250.00
      },
      {
        "id": 2,
        "sale_id": 42,
        "product_id": 2,
        "quantity": 3,
        "unit_price": 150.00,
        "total_price": 450.00
      }
    ],
    "summary": {
      "items_count": 2,
      "total_quantity": 8,
      "total_amount": 1700.00,
      "paid_amount": 2000.00,
      "change_amount": 300.00
    }
  }
}
```

### 4. Get Low Stock Report

**Request**:
```bash
curl "http://localhost:8080/api/reports/low-stock?threshold=10&limit=50"
```

**Response**:
```json
{
  "status": "success",
  "data": {
    "report_date": "2025-12-05 16:00:00",
    "products_count": 3,
    "total_stock_value": 2500.00,
    "products": [
      {
        "id": 5,
        "name": "Mouse Pad",
        "sku": "MP-001",
        "current_stock": 5,
        "reorder_level": 20,
        "unit": "piece",
        "status": "low_stock",
        "variance": 15,
        "cost_price": 50.00,
        "stock_value": 250.00
      },
      {
        "id": 8,
        "name": "USB Cable",
        "sku": "USB-001",
        "current_stock": 0,
        "reorder_level": 30,
        "unit": "piece",
        "status": "out_of_stock",
        "variance": 30,
        "cost_price": 75.00,
        "stock_value": 0
      }
    ]
  }
}
```

### 5. Get Daily Sales Report

**Request**:
```bash
curl "http://localhost:8080/api/reports/daily-sales?date=2025-12-05"
```

**Response**:
```json
{
  "status": "success",
  "data": {
    "date": "2025-12-05",
    "total_transactions": 15,
    "total_revenue": 25750.00,
    "total_items_sold": 87,
    "average_transaction_value": 1716.67,
    "payment_methods": {
      "cash": {
        "count": 10,
        "amount": 18500.00
      },
      "card": {
        "count": 4,
        "amount": 6250.00
      },
      "check": {
        "count": 1,
        "amount": 1000.00
      }
    },
    "sales": [...]
  }
}
```

### 6. Get Monthly Sales Report

**Request**:
```bash
curl "http://localhost:8080/api/reports/monthly-sales?year=2025&month=12"
```

**Response**:
```json
{
  "status": "success",
  "data": {
    "year": 2025,
    "month": 12,
    "month_name": "December",
    "total_revenue": 125680.50,
    "total_transactions": 78,
    "average_daily_revenue": 4189.35,
    "daily_breakdown": [
      {
        "day": "01",
        "date": "2025-12-01",
        "transactions": 5,
        "revenue": 8750.00
      },
      {
        "day": "05",
        "date": "2025-12-05",
        "transactions": 15,
        "revenue": 25750.00
      }
    ]
  }
}
```

---

## 💻 Model Usage Examples

### Using Product Model

```php
<?php
use App\Models\ProductModel;

$productModel = new ProductModel();

// Get all products
$products = $productModel->findAll();

// Get single product
$product = $productModel->find(1);

// Get low stock products
$lowStockProducts = $productModel->getLowStock();

// Check stock availability
if ($productModel->hasStock(1, 5)) {
    echo "5 units available";
}

// Update stock
$productModel->updateStock(1, 5, 'decrease'); // Reduce by 5
$productModel->updateStock(1, 10, 'increase'); // Increase by 10

// Get products by supplier
$supplierProducts = $productModel->bySupplier(3);

// Get product with supplier details
$productWithSupplier = $productModel->withSupplier(1);
```

### Using Sale Model with Relationships

```php
<?php
use App\Models\SaleModel;

$saleModel = new SaleModel();

// Get sale with related data
$sale = $saleModel->find(42);

// Get sale items (if relationship loaded)
$saleItems = $sale->saleItems(); // Requires relationship method

// Query with relationships
$sales = $saleModel
    ->where('customer_name', 'Maria')
    ->orderBy('created_at', 'DESC')
    ->findAll();

// Get today's sales
$todaySales = $saleModel
    ->where('DATE(created_at)', date('Y-m-d'))
    ->findAll();
```

### Using Database Transactions

```php
<?php
use Config\Database;

$db = Database::connect();
$db->transBegin();

try {
    // Create sale
    $saleModel = new SaleModel();
    $saleId = $saleModel->insert([
        'invoice_no' => 'INV-123',
        'customer_name' => 'John Doe',
        'total_amount' => 1500.00,
        'paid_amount' => 1500.00,
        'change_amount' => 0,
        'status' => 'completed'
    ]);

    // Create sale items
    $itemModel = new SaleItemModel();
    $itemModel->insert([
        'sale_id' => $saleId,
        'product_id' => 1,
        'quantity' => 5,
        'unit_price' => 250.00,
        'total_price' => 1250.00
    ]);

    // Update stock
    $productModel = new ProductModel();
    $productModel->updateStock(1, 5, 'decrease');

    // Commit if everything is OK
    $db->transCommit();
    
} catch (Exception $e) {
    // Rollback on error
    $db->transRollback();
    throw $e;
}
```

---

## 🎯 Controller Method Examples

### PosController - Checkout Method

```php
public function checkout()
{
    // Get JSON request
    $data = $this->request->getJSON(true);
    
    // Validate
    $rules = [
        'items' => 'required',
        'customer_name' => 'required',
        'payment_method' => 'required|in_list[cash,card,check,online]',
        'paid_amount' => 'required|numeric'
    ];

    if (!$this->validateData($data, $rules)) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => $this->errors
        ])->setStatusCode(422);
    }

    $items = $data['items'];
    $productModel = new ProductModel();
    $totalAmount = 0;

    // Validate each item and check stock
    foreach ($items as $item) {
        if (!$productModel->hasStock($item['product_id'], $item['quantity'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Insufficient stock'
            ])->setStatusCode(422);
        }
        $totalAmount += $item['quantity'] * $item['unit_price'];
    }

    // Start transaction
    $this->db->transBegin();

    try {
        // Process sale
        // ...
        $this->db->transCommit();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $saleData
        ])->setStatusCode(201);

    } catch (Exception $e) {
        $this->db->transRollback();
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ])->setStatusCode(500);
    }
}
```

### ReportsController - Low Stock Method

```php
public function lowStock()
{
    $threshold = $this->request->getVar('threshold');
    
    $productModel = new ProductModel();
    
    $products = $productModel
        ->where('current_stock <=', $threshold ?? 'reorder_level')
        ->where('is_active', 1)
        ->findAll();

    return $this->response->setJSON([
        'status' => 'success',
        'data' => [
            'products_count' => count($products),
            'products' => $products
        ]
    ]);
}
```

---

## 🧪 Testing Examples

### Testing the POS Checkout

```bash
# Test successful checkout
curl -X POST http://localhost:8080/api/pos/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{"product_id": 1, "quantity": 2, "unit_price": 100}],
    "customer_name": "Test Customer",
    "payment_method": "cash",
    "paid_amount": 300
  }'

# Expected: 201 Created with sale data

# Test insufficient stock
curl -X POST http://localhost:8080/api/pos/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{"product_id": 1, "quantity": 999, "unit_price": 100}],
    "customer_name": "Test",
    "payment_method": "cash",
    "paid_amount": 100000
  }'

# Expected: 422 Unprocessable Entity with stock error

# Test insufficient payment
curl -X POST http://localhost:8080/api/pos/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{"product_id": 1, "quantity": 5, "unit_price": 100}],
    "customer_name": "Test",
    "payment_method": "cash",
    "paid_amount": 400
  }'

# Expected: 422 with payment error
```

---

## 🔑 Key Features Recap

| Feature | Implementation | Status |
|---------|-----------------|--------|
| Product with Relationships | Model + Methods | ✅ Complete |
| Supplier Management | Model + API | ✅ Complete |
| Sale Transactions | Model + DB Transactions | ✅ Complete |
| Sale Items Tracking | Model + Relationships | ✅ Complete |
| Stock Validation | ProductModel::hasStock() | ✅ Complete |
| Stock Updates | ProductModel::updateStock() | ✅ Complete |
| POS Checkout | PosController::checkout() | ✅ Complete |
| Invoice Generation | INV-YYYYMMDD-XXXXXX | ✅ Complete |
| DB Transactions | transBegin/transCommit | ✅ Complete |
| Sales Listing | SalesController::index() | ✅ Complete |
| Sales Details | SalesController::show() | ✅ Complete |
| Low Stock Report | ReportsController::lowStock() | ✅ Complete |
| Daily Sales Report | ReportsController::dailySales() | ✅ Complete |
| Monthly Sales Report | ReportsController::monthlySales() | ✅ Complete |
| POS Interface | pos.blade.php View | ✅ Complete |
| Product Search | JavaScript Filter | ✅ Complete |
| Cart Management | JavaScript Array | ✅ Complete |
| Change Calculation | Auto-calculated | ✅ Complete |
| API Routes | routes.php | ✅ Complete |

---

## 📞 Support & Troubleshooting

### Common Issues

**Issue**: Products not loading in POS
- **Solution**: Check `/api/products` endpoint returns data
- **Debug**: `curl http://localhost:8080/api/products`

**Issue**: Checkout returns 422 error
- **Solution**: Verify all required fields in request
- **Check**: items array, customer_name, payment_method, paid_amount

**Issue**: Stock not updating after sale
- **Solution**: Check transaction commit is successful
- **Debug**: Add logging to ProductModel::updateStock()

**Issue**: Invoice numbers duplicated
- **Solution**: Ensure database has unique constraint on invoice_no
- **Migration**: ALTER TABLE sales ADD UNIQUE KEY unique_invoice (invoice_no);

---

## 🚀 Performance Tips

1. **Add Database Indexes**:
   ```sql
   CREATE INDEX idx_product_supplier ON products(supplier_id);
   CREATE INDEX idx_sale_date ON sales(created_at);
   CREATE INDEX idx_sale_item_sale ON sale_items(sale_id);
   ```

2. **Use Caching for Reports**:
   - Cache daily sales calculations
   - Cache low stock list (update every hour)

3. **Optimize Queries**:
   - Use `select()` to fetch only needed columns
   - Eager load relationships when possible
   - Paginate large datasets

---

**Last Updated**: December 5, 2025
**Status**: Production Ready
