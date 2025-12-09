# Testing Guide

## Quick Start

### 1. Run Migrations
```bash
cd c:\xampp\htdocs\FISHING
php spark migrate
```

### 2. Seed Test Users
```bash
php spark db:seed UserSeeder
```

### 3. Start Development Server
```bash
php spark serve
```

### 4. Login
Navigate to: `http://localhost:8080/auth/login`

**Demo Credentials:**
```
Email: admin@fishing.com
Password: password123
```

## Test Users

| Username | Email | Password | Role |
|----------|-------|----------|------|
| admin | admin@fishing.com | password123 | admin |
| cashier1 | cashier1@fishing.com | password123 | staff |
| staff1 | staff1@fishing.com | password123 | staff |

## Testing Workflow

### 1. Dashboard Access
After login, you should see:
- ✅ Dashboard with metrics
- ✅ Sidebar navigation
- ✅ User dropdown with logout option
- ✅ Session-aware username display

### 2. Navigation
Test these sidebar links:
- **Dashboard** - Should show metrics and charts
- **Point of Sale** - Should show POS interface (if products exist)
- **Sales** - Should show sales list (empty initially)
- **Products** - Product management
- **Suppliers** - Supplier management
- **Purchase Orders** - PO management
- **Reports** - Reports hub
- **Users** - User management (admin only)
- **Settings** - System settings (admin only)

### 3. POS Testing
1. Click "Point of Sale" in sidebar
2. Products should load from database (initially empty)
3. Add products first via Products menu
4. Then test POS:
   - Search for products
   - Add to cart
   - Verify stock validation
   - Complete checkout
   - See invoice generated

### 4. Logout Testing
1. Click username in top-right
2. Select "Logout"
3. Should redirect to login page
4. Verify session is cleared

## Database Verification

### Check Users Table
```sql
SELECT id, username, email, role, is_active FROM users;
```

### Expected Output
```
id | username  | email                  | role  | is_active
---|-----------|------------------------|-------|----------
1  | admin     | admin@fishing.com      | admin | 1
2  | cashier1  | cashier1@fishing.com   | staff | 1
3  | staff1    | staff1@fishing.com     | staff | 1
```

### Check Table Existence
```sql
SHOW TABLES;
```

Expected tables:
- users
- suppliers
- products
- sales
- sale_items
- purchase_orders
- po_items

## Common Issues & Solutions

### Issue: Blank Login Page
**Solution:** Clear browser cache and cookies, refresh page

### Issue: "Database table 'users' doesn't exist"
**Solution:** 
```bash
php spark migrate
php spark db:seed UserSeeder
```

### Issue: Login fails with correct credentials
**Solution:** 
1. Check users table has data: `SELECT COUNT(*) FROM users;`
2. Verify password hash: `php spark db:seed UserSeeder` (reseed if needed)
3. Check if `is_active = 1` for the user

### Issue: Session not persisting
**Solution:** 
1. Check `Config/Session.php` is properly configured
2. Ensure `$sessionDriver = 'files'` (or database if configured)
3. Check `writable/session/` folder permissions

### Issue: Sidebar links not working
**Solution:**
1. Verify you're logged in (check session cookie)
2. Check that AuthFilter is applied to routes
3. Verify base.php sidebar routes match Config/Routes.php

### Issue: 404 on protected routes
**Solution:**
1. Ensure authentication filter is active
2. Check that routes are defined in Config/Routes.php
3. Verify controller names match route definitions

## API Testing

### Test Authentication Check
```bash
curl -X GET http://localhost:8080/api/auth/check
```

Expected response (if logged in):
```json
{
  "authenticated": true,
  "user": {
    "id": 1,
    "username": "admin",
    "email": "admin@fishing.com",
    "role": "admin"
  }
}
```

### Test Products Endpoint
```bash
curl -X GET http://localhost:8080/api/products \
  -H "Cookie: PHPSESSID=your_session_id"
```

### Test POS Checkout (After Adding Products)
```bash
curl -X POST http://localhost:8080/api/pos/checkout \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 2, "unit_price": 100}
    ],
    "customer_name": "Test Customer",
    "payment_method": "cash",
    "paid_amount": 300
  }'
```

## Performance Testing

### Test Dashboard Load Time
1. Open browser DevTools (F12)
2. Go to Network tab
3. Navigate to `/dashboard`
4. Check loading time and requests

### Test API Response Times
1. Use PostMan or Insomnia
2. Make API requests and check response times
3. All should be < 1 second for small datasets

## Security Testing

### Test Session Expiration
1. Login successfully
2. Wait for session timeout (default 7200 seconds)
3. Try to access protected route
4. Should redirect to login

### Test CSRF Protection
1. Check login forms have `csrf_field()`
2. Verify CSRF tokens in POST requests

### Test Password Security
1. Create new user during registration
2. Verify password is hashed in database
3. Try login with new account

## Regression Testing Checklist

- [ ] Login works with correct credentials
- [ ] Login fails with incorrect credentials
- [ ] Logout clears session
- [ ] Dashboard loads after login
- [ ] Sidebar navigation works
- [ ] User dropdown shows username
- [ ] All protected routes redirect to login if not authenticated
- [ ] API endpoints return proper JSON responses
- [ ] Database migrations create all tables
- [ ] Seeders create test data

## Manual Testing Script

```bash
# 1. Clear any previous setup
php spark migrate:fresh

# 2. Run migrations
php spark migrate

# 3. Seed test data
php spark db:seed UserSeeder

# 4. Start server
php spark serve

# 5. In browser:
#    - Go to http://localhost:8080/auth/login
#    - Login with admin@fishing.com / password123
#    - Verify dashboard loads
#    - Test sidebar navigation
#    - Click logout
#    - Verify redirected to login
```

## What Works ✅

- [x] User authentication (login/register)
- [x] Session management
- [x] Password hashing and verification
- [x] Dashboard with real-time data
- [x] Sidebar navigation with active states
- [x] Logout functionality
- [x] Database migrations for all 7 tables
- [x] Authentication filter on protected routes
- [x] User roles (admin/staff) differentiation
- [x] Professional UI with Bootstrap 5

## What's Not Yet Implemented ⏳

- [ ] Product CRUD views (controllers ready)
- [ ] Supplier management UI
- [ ] Purchase order workflow UI
- [ ] Reports dashboard UI
- [ ] User management admin panel
- [ ] Settings page
- [ ] Product import/export
- [ ] Advanced filtering and search

---

**Latest Update**: Login, registration, dashboard, and authentication system fully implemented and tested.
