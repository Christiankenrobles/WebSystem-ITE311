<?= $this->extend('template') ?>

<?= $this->section('content') ?>

    <div class="users-management-container">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-users-cog"></i> User Management
                </h1>
                <p class="page-subtitle">Manage system users, roles, and permissions</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" id="openAddUserBtn">
                    <i class="fas fa-user-plus me-2"></i>Add User
                </button>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-container mb-5">
            <div class="stat-card">
                <div class="stat-icon admin">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?>
                    </h3>
                    <p class="stat-label">Admins</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon teacher">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'teacher')) ?>
                    </h3>
                    <p class="stat-label">Teachers</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon student">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'student')) ?>
                    </h3>
                    <p class="stat-label">Students</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value"><?= $totalUsers ?></h3>
                    <p class="stat-label">Total Users</p>
                </div>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="search-filter-section mb-4">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input 
                    type="text" 
                    id="userSearch" 
                    class="search-input" 
                    placeholder="Search by name or email..."
                    autocomplete="off"
                >
                <button class="search-clear" id="clearSearch">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="filter-controls">
                <select id="roleFilter" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-wrapper">
            <table class="users-table" id="usersTable">
                <thead>
                    <tr>
                        <th class="col-id">
                            <i class="fas fa-hashtag"></i> ID
                        </th>
                        <th class="col-name">
                            <i class="fas fa-user"></i> Name
                        </th>
                        <th class="col-email">
                            <i class="fas fa-envelope"></i> Email
                        </th>
                        <th class="col-role">
                            <i class="fas fa-shield-alt"></i> Role
                        </th>
                        <th class="col-created">
                            <i class="fas fa-calendar"></i> Created At
                        </th>
                        <th class="col-actions">
                            <i class="fas fa-cogs"></i> Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php foreach ($users as $user): ?>
                        <tr class="user-row" data-user-id="<?= $user['id'] ?>" data-user-role="<?= $user['role'] ?>">
                            <td class="col-id">
                                <span class="id-badge">#<?= $user['id'] ?></span>
                            </td>
                            <td class="col-name">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                    <span class="user-name"><?= esc($user['name']) ?></span>
                                </div>
                            </td>
                            <td class="col-email">
                                <span class="email-text"><?= esc($user['email']) ?></span>
                            </td>
                            <td class="col-role">
                                <?php if ($user['role'] === 'admin'): ?>
                                    <div class="role-display admin-locked">
                                        <i class="fas fa-crown"></i> Admin
                                        <span class="lock-icon" title="Admin role cannot be changed">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <select class="role-select" data-user-id="<?= $user['id'] ?>" data-original-role="<?= $user['role'] ?>">
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                            <i class="fas fa-crown"></i> Admin
                                        </option>
                                        <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>
                                            <i class="fas fa-chalkboard-user"></i> Teacher
                                        </option>
                                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>
                                            <i class="fas fa-graduation-cap"></i> Student
                                        </option>
                                    </select>
                                <?php endif; ?>
                            </td>
                            <td class="col-created">
                                <span class="created-date">
                                    <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                </span>
                                <span class="created-time">
                                    <?= date('H:i', strtotime($user['created_at'])) ?>
                                </span>
                            </td>
                            <td class="col-actions">
                                <div class="action-buttons">
                                    <a href="<?= site_url('users/edit/' . $user['id']) ?>" 
                                       class="btn-action btn-edit" 
                                       title="Edit user">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn-action btn-delete" 
                                            data-user-id="<?= $user['id'] ?>"
                                            data-user-name="<?= esc($user['name']) ?>"
                                            title="Delete user">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No users found</h3>
                    <p>Try adjusting your search or filter criteria</p>
                </div>
            <?php endif; ?>
        </div>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addUserForm">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="add_name" class="form-control" required>
                                    <small class="form-text text-muted">Only letters, numbers, and spaces are allowed. Special characters will be rejected on submit.</small>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="add_username" class="form-control" required>
                                    <small class="form-text text-muted">Only letters, numbers, and underscore (_) are allowed. Special characters will be rejected on submit.</small>
                                    <div class="invalid-feedback" id="username-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="add_email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" id="add_role" class="form-select" required>
                                        <option value="student">Student</option>
                                        <option value="teacher">Teacher</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" id="add_password" class="form-control" required minlength="8">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirm" id="add_password_confirm" class="form-control" required minlength="8">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="submitAddUser" class="btn btn-primary">Create User</button>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container mt-4">
                <ul class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1">
                                <i class="fas fa-chevron-left"></i> First
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $totalPages ?>">
                                Last <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <style>
        /* ============================================
           USER MANAGEMENT STYLES
           ============================================ */

        .users-management-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 2rem;
            border-bottom: 2px solid #ecf0f1;
        }

        .header-content h1.page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .page-title i {
            color: #3498db;
            margin-right: 0.75rem;
        }

        .page-subtitle {
            font-size: 0.95rem;
            color: #7f8c8d;
            margin: 0;
        }

        /* Statistics Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #3498db;
        }

        .stat-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .stat-card.admin {
            border-left-color: #e74c3c;
        }

        .stat-card.teacher {
            border-left-color: #f39c12;
        }

        .stat-card.student {
            border-left-color: #27ae60;
        }

        .stat-card.total {
            border-left-color: #9b59b6;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.admin {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .stat-icon.teacher {
            background: linear-gradient(135deg, #f39c12 0%, #d68910 100%);
        }

        .stat-icon.student {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }

        .stat-icon.total {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin: 0.25rem 0 0 0;
            font-weight: 500;
        }

        /* Search & Filter */
        .search-filter-section {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            color: #95a5a6;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-clear {
            position: absolute;
            right: 0.75rem;
            background: none;
            border: none;
            color: #95a5a6;
            cursor: pointer;
            padding: 0.5rem;
            display: none;
            transition: color 0.2s ease;
        }

        .search-clear:hover {
            color: #2c3e50;
        }

        .search-clear.show {
            display: block;
        }

        .filter-controls {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 0.95rem;
            background-color: white;
            color: #2c3e50;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        /* Table Wrapper */
        .table-wrapper {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        /* Users Table */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .users-table thead {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        .users-table th {
            padding: 1.25rem 1rem;
            text-align: left;
            color: white;
            font-weight: 600;
            white-space: nowrap;
        }

        .users-table th i {
            margin-right: 0.5rem;
            opacity: 0.8;
        }

        .users-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: all 0.2s ease;
        }

        .users-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .users-table td {
            padding: 1.25rem 1rem;
        }

        /* Table Columns */
        .col-id {
            width: 60px;
        }

        .col-name {
            width: 200px;
        }

        .col-email {
            width: 200px;
        }

        .col-role {
            width: 150px;
        }

        .col-created {
            width: 150px;
        }

        .col-actions {
            width: 120px;
            text-align: center;
        }

        /* ID Badge */
        .id-badge {
            display: inline-block;
            background: #ecf0f1;
            color: #2c3e50;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 500;
            color: #2c3e50;
        }

        /* Email */
        .email-text {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

            /* Role Display (Locked Admin) */
            .role-display {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.6rem 0.9rem;
                border-radius: 6px;
                font-size: 0.9rem;
                font-weight: 500;
                background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
                color: white;
                border: 2px solid #c0392b;
            }

            .role-display.admin-locked {
                cursor: not-allowed;
                opacity: 0.95;
            }

            .lock-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 18px;
                height: 18px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 3px;
                font-size: 0.7rem;
                margin-left: 0.25rem;
            }

        /* Role Select */
        .role-select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 2px solid #ecf0f1;
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: white;
            color: #2c3e50;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .role-select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .role-select.updating {
            opacity: 0.6;
            pointer-events: none;
        }

        .role-select.success {
            border-color: #27ae60;
        }

        .role-select.error {
            border-color: #e74c3c;
        }

        /* Created Date */
        .created-date {
            display: block;
            color: #2c3e50;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .created-time {
            display: block;
            color: #95a5a6;
            font-size: 0.8rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            border: none;
            background: #ecf0f1;
            color: #2c3e50;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-edit {
            color: #3498db;
            background: rgba(52, 152, 219, 0.1);
        }

        .btn-edit:hover {
            background: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }

        .btn-delete {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
        }

        .btn-delete:hover {
            background: rgba(231, 76, 60, 0.2);
            color: #c0392b;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #95a5a6;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            list-style: none;
            gap: 0.25rem;
            padding: 0;
            margin: 0;
        }

        .page-item {
            margin: 0;
        }

        .page-link {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.6rem 0.9rem;
            background: white;
            border: 2px solid #ecf0f1;
            border-radius: 6px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .page-link:hover {
            border-color: #3498db;
            color: #3498db;
        }

        .page-item.active .page-link {
            background: #3498db;
            border-color: #3498db;
            color: white;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: white;
            border-left: 4px solid #27ae60;
            padding: 1.25rem;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            z-index: 2000;
            animation: slideIn 0.3s ease;
        }

        .toast-notification.error {
            border-left-color: #e74c3c;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .users-table th,
            .users-table td {
                padding: 1rem 0.75rem;
                font-size: 0.9rem;
            }

            .col-name {
                width: 150px;
            }

            .col-email {
                width: 150px;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .header-content h1.page-title {
                font-size: 1.5rem;
            }

            .stats-container {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-card {
                padding: 1rem;
                gap: 0.75rem;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .search-filter-section {
                flex-direction: column;
            }

            .search-box {
                min-width: auto;
            }

            .filter-select {
                min-width: auto;
                flex: 1;
            }

            .users-table {
                font-size: 0.85rem;
            }

            .users-table th,
            .users-table td {
                padding: 0.75rem 0.5rem;
            }

            .col-id {
                width: 50px;
            }

            .col-name {
                width: 120px;
            }

            .col-email {
                width: 120px;
            }

            .col-role {
                width: 100px;
            }

            .col-created {
                display: none;
            }

            .created-time {
                display: none;
            }

            .user-info {
                gap: 0.5rem;
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .btn-action {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.25rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .search-box,
            .filter-select {
                width: 100%;
            }

            .col-id,
            .col-actions {
                width: auto;
            }

            .col-name,
            .col-email,
            .col-role {
                width: auto;
            }

            .id-badge {
                display: none;
            }

            .email-text {
                font-size: 0.8rem;
            }

            .pagination {
                flex-wrap: wrap;
                gap: 0.15rem;
            }

            .page-link {
                padding: 0.5rem 0.7rem;
                font-size: 0.8rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add User modal elements
            const openAddUserBtn = document.getElementById('openAddUserBtn');
            const addUserModalEl = document.getElementById('addUserModal');
            const addUserModal = addUserModalEl ? new bootstrap.Modal(addUserModalEl) : null;
            const submitAddUser = document.getElementById('submitAddUser');

            if (openAddUserBtn) {
                openAddUserBtn.addEventListener('click', function() {
                    addUserModal?.show();
                });
            }

            // Full Name field - allow typing but validate on submit
            // No real-time blocking - user can type special characters but will get warning on submit

            // Username field - allow typing but validate on submit
            // No real-time blocking - user can type special characters but will get warning on submit

            // Create user handler
            submitAddUser?.addEventListener('click', function() {
                const name = document.getElementById('add_name').value.trim();
                const username = document.getElementById('add_username').value.trim();
                const email = document.getElementById('add_email').value.trim();
                const role = document.getElementById('add_role').value;
                const password = document.getElementById('add_password').value;
                const password_confirm = document.getElementById('add_password_confirm').value;

                // Clear previous errors
                document.getElementById('name-error').textContent = '';
                document.getElementById('username-error').textContent = '';
                document.getElementById('add_name').classList.remove('is-invalid');
                document.getElementById('add_username').classList.remove('is-invalid');

                // Collect all errors
                const errors = [];
                let hasError = false;

                // Validate required fields
                if (!name) {
                    errors.push('❌ Full Name is required');
                    document.getElementById('add_name').classList.add('is-invalid');
                    document.getElementById('name-error').textContent = 'Full Name is required';
                    hasError = true;
                }

                if (!username) {
                    errors.push('❌ Username is required');
                    document.getElementById('add_username').classList.add('is-invalid');
                    document.getElementById('username-error').textContent = 'Username is required';
                    hasError = true;
                }

                if (!email) {
                    errors.push('❌ Email is required');
                    hasError = true;
                }

                if (!password) {
                    errors.push('❌ Password is required');
                    hasError = true;
                }

                // Validate name - no special characters (only letters, numbers, spaces)
                if (name) {
                    const namePattern = /^[A-Za-z0-9\s]+$/;
                    if (!namePattern.test(name)) {
                        errors.push('⚠️ FULL NAME ERROR: Contains special characters\n   Allowed: Letters, Numbers, Spaces only');
                        document.getElementById('add_name').classList.add('is-invalid');
                        document.getElementById('name-error').textContent = 'Name cannot contain special characters. Only letters, numbers, and spaces are allowed.';
                        hasError = true;
                    }
                }

                // Validate username - no special characters (only letters, numbers, underscore)
                if (username) {
                    const usernamePattern = /^[A-Za-z0-9_]+$/;
                    if (!usernamePattern.test(username)) {
                        errors.push('⚠️ USERNAME ERROR: Contains special characters\n   Allowed: Letters, Numbers, Underscore (_) only');
                        document.getElementById('add_username').classList.add('is-invalid');
                        document.getElementById('username-error').textContent = 'Username cannot contain special characters. Only letters, numbers, and underscore (_) are allowed.';
                        hasError = true;
                    }
                }

                // Validate email format
                if (email) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(email)) {
                        errors.push('⚠️ EMAIL ERROR: Invalid email format\n   Example: user@example.com');
                        hasError = true;
                    }
                }

                // Validate password match
                if (password && password_confirm) {
                    if (password !== password_confirm) {
                        errors.push('⚠️ PASSWORD ERROR: Passwords do not match');
                        hasError = true;
                    }
                }

                // Show all errors if any
                if (hasError) {
                    let errorMessage = '⚠️ VALIDATION ERRORS:\n\n';
                    errorMessage += errors.join('\n\n');
                    errorMessage += '\n\nPlease fix the errors above and try again.';
                    
                    alert(errorMessage);
                    
                    // Focus on first error field
                    if (document.getElementById('add_name').classList.contains('is-invalid')) {
                        document.getElementById('add_name').focus();
                    } else if (document.getElementById('add_username').classList.contains('is-invalid')) {
                        document.getElementById('add_username').focus();
                    }
                    
                    return false;
                }

                submitAddUser.disabled = true;

                fetch('<?= site_url('users/create') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_token() ?>'
                    },
                    body: JSON.stringify({ name, username, email, role, password })
                })
                .then(r => r.json())
                .then(data => {
                    submitAddUser.disabled = false;
                    if (data.success) {
                        addUserModal?.hide();
                        showNotification('User created successfully', 'success');
                        // Append new row to table (simple append - may refresh for full data)
                        appendUserRow(data.user);
                        // Update totals
                        const totalEl = document.querySelector('.stat-card.total .stat-value');
                        if (totalEl) totalEl.textContent = parseInt(totalEl.textContent) + 1;
                    } else {
                        showNotification(data.message || 'Failed to create user', 'error');
                    }
                }).catch(err => {
                    console.error(err);
                    submitAddUser.disabled = false;
                    showNotification('Error creating user', 'error');
                });
            });

            // Helper to append a user row
            function appendUserRow(user) {
                if (!user) return;
                const tbody = document.getElementById('usersTableBody');
                const tr = document.createElement('tr');
                tr.className = 'user-row';
                tr.setAttribute('data-user-id', user.id);
                tr.setAttribute('data-user-role', user.role);
                tr.innerHTML = `
                    <td class="col-id"><span class="id-badge">#${user.id}</span></td>
                    <td class="col-name"><div class="user-info"><div class="user-avatar">${user.name.charAt(0).toUpperCase()}</div><span class="user-name">${escapeHtml(user.name)}</span></div></td>
                    <td class="col-email"><span class="email-text">${escapeHtml(user.email)}</span></td>
                    <td class="col-role">
                        ${user.role === 'admin' ? `<div class="role-display admin-locked"><i class="fas fa-crown"></i> Admin <span class="lock-icon"><i class="fas fa-lock"></i></span></div>` : `<select class="role-select" data-user-id="${user.id}" data-original-role="${user.role}"><option value="admin">Admin</option><option value="teacher">Teacher</option><option value="student">Student</option></select>`}
                    </td>
                    <td class="col-created"><span class="created-date">${new Date(user.created_at).toLocaleDateString()}</span><span class="created-time">${new Date(user.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span></td>
                    <td class="col-actions"><div class="action-buttons"><a href="<?= site_url('users/edit/') ?>${user.id}" class="btn-action btn-edit" title="Edit user"><i class="fas fa-edit"></i></a><button class="btn-action btn-delete" data-user-id="${user.id}" data-user-name="${escapeHtml(user.name)}" title="Delete user"><i class="fas fa-trash"></i></button></div></td>
                `;
                tbody.prepend(tr);
                // attach change handler to new select
                const sel = tr.querySelector('.role-select');
                if (sel) sel.addEventListener('change', handleRoleChange);
                // attach delete handler
                const delBtn = tr.querySelector('.btn-delete');
                if (delBtn) delBtn.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    if (confirm(`Are you sure you want to delete "${userName}"?`)) {
                        fetch('<?= site_url('users/delete') ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', '<?= csrf_header() ?>': '<?= csrf_token() ?>' },
                            body: JSON.stringify({ user_id: userId })
                        }).then(r => r.json()).then(d => { if (d.success) tr.remove(); else showNotification(d.message||'Failed', 'error'); });
                    }
                });
            }

            function escapeHtml(s) { return (s+'').replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c]; }); }
            const userSearch = document.getElementById('userSearch');
            const clearSearch = document.getElementById('clearSearch');
            const roleFilter = document.getElementById('roleFilter');
            const roleSelects = document.querySelectorAll('.role-select');

            // Search functionality
            userSearch?.addEventListener('input', function() {
                filterTable();
                clearSearch.classList.toggle('show', this.value.length > 0);
            });

            clearSearch?.addEventListener('click', function() {
                userSearch.value = '';
                filterTable();
                clearSearch.classList.remove('show');
                userSearch.focus();
            });

            // Role filter
            roleFilter?.addEventListener('change', filterTable);

            // Role change handler
            roleSelects.forEach(select => {
                select.addEventListener('change', handleRoleChange);
            });

            // Filter table function
            function filterTable() {
                const searchTerm = userSearch.value.toLowerCase();
                const selectedRole = roleFilter.value;
                const rows = document.querySelectorAll('.user-row');

                rows.forEach(row => {
                    const name = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                    const email = row.querySelector('.email-text')?.textContent.toLowerCase() || '';
                    const role = row.getAttribute('data-user-role');

                    const matchSearch = name.includes(searchTerm) || email.includes(searchTerm);
                    const matchRole = !selectedRole || role === selectedRole;

                    row.style.display = matchSearch && matchRole ? '' : 'none';
                });
            }

            // Update user role
            function handleRoleChange(event) {
                const select = event.target;
                const userId = select.getAttribute('data-user-id');
                const newRole = select.value;
                const originalRole = select.getAttribute('data-original-role');

                // Add updating state
                select.classList.add('updating');

                // Call update function
                updateUserRole(userId, newRole, function(success) {
                    select.classList.remove('updating');

                    if (success) {
                        select.classList.add('success');
                        select.setAttribute('data-original-role', newRole);
                        document.querySelector(`[data-user-id="${userId}"]`).setAttribute('data-user-role', newRole);
                        showNotification('Role updated successfully!', 'success');

                        // Remove success class after animation
                        setTimeout(() => select.classList.remove('success'), 2000);
                    } else {
                        select.classList.add('error');
                        select.value = originalRole;
                        showNotification('Failed to update role. Please try again.', 'error');

                        // Remove error class after animation
                        setTimeout(() => select.classList.remove('error'), 2000);
                    }
                });
            }

            // Update user role via AJAX
            function updateUserRole(userId, newRole, callback) {
                fetch('<?= site_url('users/update-role') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_token() ?>'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        role: newRole
                    })
                })
                .then(response => response.json())
                .then(data => {
                    callback(data.success);
                })
                .catch(error => {
                    console.error('Error:', error);
                    callback(false);
                });
            }

            // Show notification
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `toast-notification ${type}`;
                notification.innerHTML = `
                    <strong>${type === 'success' ? '✓' : '✕'}</strong> ${message}
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            // Delete user
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');

                    if (confirm(`Are you sure you want to delete "${userName}"? This action cannot be undone.`)) {
                        fetch('<?= site_url('users/delete') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                '<?= csrf_header() ?>': '<?= csrf_token() ?>'
                            },
                            body: JSON.stringify({
                                user_id: userId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`[data-user-id="${userId}"]`).remove();
                                showNotification('User deleted successfully!', 'success');
                            } else {
                                showNotification(data.message || 'Failed to delete user', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

<?= $this->endSection() ?>
