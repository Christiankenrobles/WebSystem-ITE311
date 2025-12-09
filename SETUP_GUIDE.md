# Setup Guide - Supplies Inventory & Sales System

## Installation & Database Setup

### Step 1: Run Database Migrations

Run all migrations to create the database tables:

```bash
php spark migrate
```

This will create the following tables:
- `users` - User accounts with roles (admin/staff)
- `suppliers` - Supplier information
- `products` - Product catalog
- `sales` - Sales transactions
- `sale_items` - Line items in each sale
- `purchase_orders` - Purchase orders from suppliers
- `po_items` - Line items in each purchase order

### Step 2: Seed Test Data

Create test users with the UserSeeder:

```bash
php spark db:seed UserSeeder
```

This creates 3 test users:
- **Admin Account**: Email: `admin@fishing.com` | Password: `password123`
- **Cashier**: Email: `cashier1@fishing.com` | Password: `password123`
- **Staff**: Email: `staff1@fishing.com` | Password: `password123`

## System Features

### Authentication
- Login/Register with email validation
- Password hashing using bcrypt
- Session-based authentication
- Role-based access control (admin/staff)
- Automatic logout with `/auth/logout`

### Dashboard
- Real-time sales metrics
- Stock value overview
- Low stock alerts
- 30-day sales trend chart
- Recent transactions
- Monthly revenue comparison

### Point of Sale (POS)
- Product search functionality
- Add items to cart
- Real-time stock validation
- Automatic change calculation
- Multiple payment methods (cash, card, check, online)
- Invoice generation (INV-YYYYMMDD-XXXXXX format)
- Atomic database transactions

### Inventory Management
- Product catalog with SKU tracking
- Supplier relationships
- Stock level monitoring
- Reorder level alerts
- Purchase order management
- Receiving workflow

### Reports
- Low stock reports
- Daily sales analysis
- Monthly sales trends
- Supplier performance metrics
- Payment method breakdown

## API Endpoints

### Authentication
- `GET /auth/login` - Login page
- `POST /auth/process-login` - Process login
- `GET /auth/register` - Register page
- `POST /auth/process-register` - Process registration
- `GET /auth/logout` - Logout
- `GET /api/auth/check` - Check authentication status

### Protected Routes (Require Login)
All routes below require authentication via the `AuthFilter`

#### Products API
- `GET /api/products` - List all products
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get product details
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `GET /api/products/low-stock` - Get low stock products

#### Suppliers API
- `GET /api/suppliers` - List all suppliers
- `POST /api/suppliers` - Create supplier
- `GET /api/suppliers/{id}` - Get supplier details
- `PUT /api/suppliers/{id}` - Update supplier
- `DELETE /api/suppliers/{id}` - Delete supplier

#### Sales API
- `GET /api/sales` - List all sales
- `GET /api/sales/{id}` - Get sale details
- `GET /api/sales/summary/daily` - Daily sales summary

#### POS API
- `POST /api/pos/checkout` - Complete POS transaction

#### Reports API
- `GET /api/reports/low-stock` - Low stock report
- `GET /api/reports/daily-sales` - Daily sales report
- `GET /api/reports/monthly-sales` - Monthly sales report
- `GET /api/reports/supplier-analysis` - Supplier analysis

#### Purchase Orders API
- `GET /api/purchase-orders` - List all POs
- `POST /api/purchase-orders` - Create PO
- `GET /api/purchase-orders/{id}` - Get PO details
- `PUT /api/purchase-orders/{id}` - Update PO
- `DELETE /api/purchase-orders/{id}` - Delete PO
- `POST /api/purchase-orders/{id}/receive` - Mark as received

### Web Routes (All Require Login)
- `GET /dashboard` - Main dashboard
- `GET /dashboard/sales-trend` - 30-day sales trend (JSON)
- `GET /pos` - Point of Sale interface
- `GET /products` - Products list
- `GET /suppliers` - Suppliers list
- `GET /sales` - Sales list
- `GET /purchase-orders` - Purchase orders list
- `GET /reports` - Reports hub
- `GET /profile` - User profile
- `GET /admin/users` - User management
- `GET /admin/settings` - System settings

## Login & Testing

1. Navigate to `http://localhost:8080/auth/login`
2. Use demo credentials:
   - Email: `admin@fishing.com`
   - Password: `password123`
3. You'll be redirected to the dashboard
4. Use the sidebar to navigate to different modules

## Key Features Implemented

✅ **Complete Authentication System**
- Login with email validation
- Secure password hashing (bcrypt)
- Session management
- Logout functionality
- Role-based routes

✅ **Functional Dashboard**
- Real-time metrics (today's revenue, transaction count, stock value)
- Interactive charts (sales trend, monthly comparison)
- Low stock alerts
- Recent transactions table

✅ **POS System**
- Product search
- Shopping cart
- Stock validation
- Automatic invoice generation
- Transaction handling with database atomicity

✅ **Database Schema**
- 7 well-designed tables with proper foreign keys
- Timestamps for audit trail
- Validation rules in models
- Relationships: belongsTo, hasMany

✅ **API Routes**
- 30+ RESTful endpoints
- Proper HTTP status codes
- JSON responses
- Authentication filter on protected routes

✅ **UI/UX**
- Bootstrap 5 responsive design
- Gradient color scheme
- Functional sidebar navigation
- Session-aware components
- Professional styling

## Admin Tasks

### Create a new admin user via database:
```sql
INSERT INTO users (username, email, password, role, is_active, created_at, updated_at)
VALUES ('newadmin', 'newadmin@fishing.com', '$2y$10$...', 'admin', 1, NOW(), NOW());
```

### Check database tables:
```bash
php spark db:table users
```

### Reset migrations:
```bash
php spark migrate:fresh --seed
```

## Troubleshooting

**Login not working?**
- Ensure migrations have been run: `php spark migrate`
- Check that users table has test data: `php spark db:seed UserSeeder`
- Verify password is `password123` (case-sensitive)

**404 errors on routes?**
- Ensure you're logged in (redirected to login if not)
- Check that the AuthFilter is properly configured in `Config/Filters.php`

**Session/logout issues?**
- Clear browser cookies
- Restart CodeIgniter development server: `php spark serve`

## Next Steps

1. **Create Product Controllers** - Build CRUD views for product management
2. **Create Supplier Controllers** - Implement supplier management UI
3. **Create Purchase Order Views** - Build PO entry and receiving interface
4. **Implement Reports UI** - Create interactive report dashboards
5. **Add User Management** - Admin interface for user management

---
**System Status**: ✅ Authentication & Dashboard Complete | ✅ Database Migrations Ready | ✅ API Routes Configured
