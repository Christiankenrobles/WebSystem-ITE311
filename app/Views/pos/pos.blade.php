<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale (POS) - Supplies Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .pos-container {
            display: flex;
            height: 100vh;
            gap: 15px;
            padding: 15px;
        }

        .pos-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .pos-right {
            width: 400px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .cart-section {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .checkout-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .search-box {
            margin-bottom: 20px;
        }

        .search-box input {
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 16px;
        }

        .products-list {
            flex: 1;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .product-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
            transform: translateY(-2px);
        }

        .product-card h6 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card .price {
            color: #28a745;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .product-card .stock {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .btn-add-product {
            width: 100%;
            padding: 6px;
            font-size: 12px;
        }

        .cart-header {
            padding: 15px;
            border-bottom: 2px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .cart-header h5 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .cart-table {
            font-size: 13px;
            margin-bottom: 0;
        }

        .cart-table thead {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
        }

        .cart-table th, .cart-table td {
            padding: 8px;
            vertical-align: middle;
        }

        .cart-empty {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .cart-footer {
            border-top: 2px solid #e9ecef;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .summary-total {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        .input-group-sm {
            margin-bottom: 12px;
        }

        .input-group-sm label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .input-group-sm input {
            font-size: 14px;
            padding: 8px 12px;
        }

        .btn-checkout {
            width: 100%;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn-clear-cart {
            width: 100%;
            padding: 8px;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .remove-btn {
            padding: 2px 6px;
            font-size: 11px;
        }

        .alert-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }

        .loading-spinner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner.active {
            display: flex;
        }
    </style>
</head>
<body>
    <div class="pos-container">
        <!-- Left Side: Products -->
        <div class="pos-left">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search products by name or SKU...">
            </div>
            <div class="products-list" id="productsList">
                <!-- Products will be loaded here -->
            </div>
        </div>

        <!-- Right Side: Cart & Checkout -->
        <div class="pos-right">
            <!-- Cart Section -->
            <div class="cart-section">
                <div class="cart-header">
                    <h5>
                        <i class="fas fa-shopping-cart"></i> Shopping Cart
                        <span class="badge bg-danger ms-2" id="cartCount">0</span>
                    </h5>
                </div>
                <div class="cart-body" id="cartBody">
                    <div class="cart-empty">
                        <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <p>Cart is empty</p>
                    </div>
                </div>
                <div class="cart-footer">
                    <button class="btn btn-sm btn-outline-danger btn-clear-cart" id="clearCartBtn">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </div>
            </div>

            <!-- Checkout Section -->
            <div class="checkout-section">
                <h5 class="mb-3"><i class="fas fa-credit-card"></i> Checkout</h5>

                <div class="input-group-sm">
                    <label for="customerName">Customer Name</label>
                    <input type="text" class="form-control" id="customerName" placeholder="Enter customer name">
                </div>

                <div class="input-group-sm">
                    <label for="totalAmount">Total Amount</label>
                    <input type="text" class="form-control" id="totalAmount" readonly value="0.00">
                </div>

                <div class="input-group-sm">
                    <label for="paymentMethod">Payment Method</label>
                    <select class="form-select" id="paymentMethod">
                        <option value="cash">Cash</option>
                        <option value="card">Credit/Debit Card</option>
                        <option value="check">Check</option>
                        <option value="online">Online Transfer</option>
                    </select>
                </div>

                <div class="input-group-sm">
                    <label for="paidAmount">Amount Paid</label>
                    <input type="number" class="form-control" id="paidAmount" placeholder="0.00" step="0.01" min="0">
                </div>

                <div class="input-group-sm">
                    <label for="changeAmount">Change</label>
                    <input type="text" class="form-control" id="changeAmount" readonly value="0.00">
                </div>

                <button class="btn btn-success btn-checkout" id="checkoutBtn">
                    <i class="fas fa-check-circle"></i> Complete Sale
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_BASE = '/api';
        let cart = [];
        let allProducts = [];

        // ========================================
        // DOM Elements
        // ========================================
        const searchInput = document.getElementById('searchInput');
        const productsList = document.getElementById('productsList');
        const cartBody = document.getElementById('cartBody');
        const cartCount = document.getElementById('cartCount');
        const customerName = document.getElementById('customerName');
        const totalAmount = document.getElementById('totalAmount');
        const paymentMethod = document.getElementById('paymentMethod');
        const paidAmount = document.getElementById('paidAmount');
        const changeAmount = document.getElementById('changeAmount');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const clearCartBtn = document.getElementById('clearCartBtn');
        const alertContainer = document.getElementById('alertContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');

        // ========================================
        // Initialize
        // ========================================
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            setupEventListeners();
        });

        // ========================================
        // Load Products
        // ========================================
        async function loadProducts() {
            try {
                showLoading(true);
                const response = await fetch(`${API_BASE}/products`);
                const data = await response.json();

                if (data.status === 'success') {
                    allProducts = data.data || [];
                    displayProducts(allProducts);
                } else {
                    showAlert('Error loading products', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Failed to load products', 'danger');
            } finally {
                showLoading(false);
            }
        }

        // ========================================
        // Display Products
        // ========================================
        function displayProducts(products) {
            productsList.innerHTML = '';

            products.forEach(product => {
                const hasStock = product.current_stock > 0;
                const card = document.createElement('div');
                card.className = `product-card ${!hasStock ? 'opacity-50' : ''}`;
                card.innerHTML = `
                    <h6 title="${product.name}">${product.name}</h6>
                    <div class="price">₱${parseFloat(product.sell_price).toFixed(2)}</div>
                    <div class="stock">Stock: ${product.current_stock}</div>
                    <button class="btn btn-sm btn-primary btn-add-product" ${!hasStock ? 'disabled' : ''} onclick="addToCart(${product.id})">
                        <i class="fas fa-plus"></i> Add
                    </button>
                `;
                productsList.appendChild(card);
            });
        }

        // ========================================
        // Search Products
        // ========================================
        function filterProducts() {
            const query = searchInput.value.toLowerCase();
            const filtered = allProducts.filter(p =>
                p.name.toLowerCase().includes(query) ||
                p.sku.toLowerCase().includes(query)
            );
            displayProducts(filtered);
        }

        // ========================================
        // Add to Cart
        // ========================================
        function addToCart(productId) {
            const product = allProducts.find(p => p.id === productId);
            if (!product) return;

            const existingItem = cart.find(item => item.product_id === productId);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    product_id: productId,
                    product_name: product.name,
                    unit_price: product.sell_price,
                    quantity: 1,
                    stock: product.current_stock
                });
            }

            updateCart();
            showAlert(`${product.name} added to cart`, 'success');
        }

        // ========================================
        // Remove from Cart
        // ========================================
        function removeFromCart(productId) {
            cart = cart.filter(item => item.product_id !== productId);
            updateCart();
        }

        // ========================================
        // Update Quantity
        // ========================================
        function updateQuantity(productId, quantity) {
            const item = cart.find(i => i.product_id === productId);
            if (item) {
                if (quantity <= 0) {
                    removeFromCart(productId);
                } else if (quantity <= item.stock) {
                    item.quantity = quantity;
                    updateCart();
                } else {
                    showAlert(`Only ${item.stock} items available`, 'warning');
                }
            }
        }

        // ========================================
        // Update Cart Display
        // ========================================
        function updateCart() {
            // Update count
            cartCount.textContent = cart.length;

            // Update cart body
            if (cart.length === 0) {
                cartBody.innerHTML = `
                    <div class="cart-empty">
                        <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <p>Cart is empty</p>
                    </div>
                `;
            } else {
                const html = `
                    <table class="cart-table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            ${cart.map(item => `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" style="width: 50px;" 
                                               value="${item.quantity}" 
                                               onchange="updateQuantity(${item.product_id}, this.value)"
                                               min="1" max="${item.stock}">
                                    </td>
                                    <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                                    <td>₱${(item.quantity * item.unit_price).toFixed(2)}</td>
                                    <td>
                                        <button class="btn btn-xs btn-danger remove-btn" onclick="removeFromCart(${item.product_id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                cartBody.innerHTML = html;
            }

            // Calculate total
            const total = cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            totalAmount.value = total.toFixed(2);

            // Calculate change
            const paid = parseFloat(paidAmount.value) || 0;
            changeAmount.value = (paid - total).toFixed(2);
        }

        // ========================================
        // Checkout
        // ========================================
        async function checkout() {
            // Validation
            if (cart.length === 0) {
                showAlert('Cart is empty', 'warning');
                return;
            }

            if (!customerName.value.trim()) {
                showAlert('Please enter customer name', 'warning');
                return;
            }

            if (!paidAmount.value || parseFloat(paidAmount.value) <= 0) {
                showAlert('Please enter paid amount', 'warning');
                return;
            }

            const total = parseFloat(totalAmount.value);
            const paid = parseFloat(paidAmount.value);

            if (paid < total) {
                showAlert('Paid amount is less than total', 'warning');
                return;
            }

            // Prepare payload
            const payload = {
                items: cart.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price
                })),
                customer_name: customerName.value,
                payment_method: paymentMethod.value,
                paid_amount: paid
            };

            try {
                showLoading(true);
                const response = await fetch(`${API_BASE}/pos/checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showAlert(`Sale completed! Invoice: ${data.data.invoice_no}`, 'success');
                    cart = [];
                    updateCart();
                    customerName.value = '';
                    paidAmount.value = '';
                    changeAmount.value = '0.00';
                    loadProducts(); // Refresh products
                } else {
                    showAlert(data.message || 'Checkout failed', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Checkout error: ' + error.message, 'danger');
            } finally {
                showLoading(false);
            }
        }

        // ========================================
        // Event Listeners
        // ========================================
        function setupEventListeners() {
            searchInput.addEventListener('input', filterProducts);
            checkoutBtn.addEventListener('click', checkout);
            clearCartBtn.addEventListener('click', () => {
                if (cart.length > 0 && confirm('Clear the entire cart?')) {
                    cart = [];
                    updateCart();
                }
            });
            paidAmount.addEventListener('input', () => {
                const total = parseFloat(totalAmount.value) || 0;
                const paid = parseFloat(paidAmount.value) || 0;
                changeAmount.value = (paid - total).toFixed(2);
            });
        }

        // ========================================
        // Utilities
        // ========================================
        function showAlert(message, type = 'info') {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show alert-message`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 4000);
        }

        function showLoading(show) {
            loadingSpinner.classList.toggle('active', show);
        }
    </script>
</body>
</html>
