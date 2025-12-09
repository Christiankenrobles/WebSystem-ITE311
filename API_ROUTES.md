# API Routes Reference

## Base URL
```
http://localhost:8080/api
```

## Product Endpoints

### List all products
```
GET /api/products
Query params: ?page=1&per_page=20&category_id=1&supplier_id=1
Response: { data: [...], total: 100, page: 1 }
```

### Get single product
```
GET /api/products/:id
Response: { id, name, sku, price, cost, stock, ... }
```

### Create product
```
POST /api/products
Body: { name, sku, category_id, supplier_id, price, cost, unit, ... }
Response: { id, ... }
```

### Update product
```
PUT /api/products/:id
Body: { name, price, cost, ... }
Response: { success: true }
```

### Delete product
```
DELETE /api/products/:id
Response: { success: true }
```

### Get low stock products
```
GET /api/products/low-stock
Query params: ?threshold=10
Response: { data: [...], count: 5 }
```

### Get products by category
```
GET /api/products/by-category/:category_id
Response: { data: [...] }
```

---

## Supplier Endpoints

### List all suppliers
```
GET /api/suppliers
Query params: ?page=1&per_page=20
Response: { data: [...], total: 50 }
```

### Get single supplier
```
GET /api/suppliers/:id
Response: { id, name, email, phone, address, ... }
```

### Create supplier
```
POST /api/suppliers
Body: { name, email, phone, address, city, ... }
Response: { id, ... }
```

### Update supplier
```
PUT /api/suppliers/:id
Body: { name, email, phone, ... }
Response: { success: true }
```

### Delete supplier
```
DELETE /api/suppliers/:id
Response: { success: true }
```

---

## Sales Endpoints

### List all sales
```
GET /api/sales
Query params: ?page=1&per_page=20&date_from=2025-01-01&date_to=2025-12-31
Response: { data: [...], total: 500 }
```

### Get sale details
```
GET /api/sales/:id
Response: { 
  id, date, total, items: [...], 
  customer_name, payment_method, ... 
}
```

### Create new sale
```
POST /api/sales
Body: { 
  items: [{ product_id, quantity, price }, ...],
  customer_name, payment_method, ... 
}
Response: { id, receipt_url }
```

### Quick add sale item (POS)
```
POST /api/sales/quick-add
Body: { product_id, quantity }
Response: { item_id, price, subtotal, ... }
```

### Get daily sales
```
GET /api/sales/daily
Query params: ?date=2025-12-05
Response: { date, total_sales, item_count, revenue, ... }
```

---

## Reports Endpoints

### Low stock report
```
GET /api/reports/low-stock
Query params: ?threshold=10&limit=50
Response: { 
  data: [
    { product_id, name, current_stock, min_stock, status }
  ]
}
```

### Daily sales report
```
GET /api/reports/daily-sales
Query params: ?date=2025-12-05
Response: {
  date, total_transactions, revenue, items_sold,
  top_products: [...],
  payment_methods: { cash, card, ... }
}
```

### Monthly sales report
```
GET /api/reports/monthly-sales
Query params: ?year=2025&month=12
Response: {
  month, year, total_revenue, transactions_count,
  daily_breakdown: [...],
  category_breakdown: [...]
}
```

### Supplier analysis
```
GET /api/reports/supplier-analysis
Query params: ?supplier_id=1&period=monthly
Response: {
  supplier_id, supplier_name,
  total_purchased, items_count, avg_lead_time,
  performance_rating, ...
}
```

---

## Response Format

### Success Response
```json
{
  "status": "success",
  "data": {...},
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "status": "error",
  "error": "validation_failed",
  "message": "The following fields have errors",
  "errors": {
    "email": "Email is required",
    "price": "Price must be a positive number"
  }
}
```

### Pagination Response
```json
{
  "status": "success",
  "data": [...],
  "pagination": {
    "page": 1,
    "per_page": 20,
    "total": 500,
    "total_pages": 25
  }
}
```

---

## HTTP Status Codes

- `200` - OK (successful request)
- `201` - Created (resource created)
- `204` - No Content (successful, no data)
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (not authorized)
- `404` - Not Found (resource not found)
- `422` - Unprocessable Entity (invalid data)
- `500` - Internal Server Error

---

## Authentication

All API endpoints (except /auth) require Bearer token:

```
Authorization: Bearer {token}
```

### Get Auth Token
```
POST /auth/login
Body: { email, password }
Response: { token, user: { id, name, email, role } }
```

---

## Rate Limiting

- 100 requests per minute per user
- 1000 requests per hour per API key

---

**Last Updated**: December 5, 2025
