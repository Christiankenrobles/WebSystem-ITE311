<?php
/**
 * Professional Header Component
 * Clean, modern, and responsive header with system title and navigation
 */
?>

<header class="professional-header">
    <div class="header-container">
        <!-- Logo/System Title -->
        <div class="header-logo">
            <a href="<?= site_url('/') ?>" class="brand-link">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="brand-text">
                    <span class="system-title">Academic Management System</span>
                </div>
            </a>
        </div>

        <!-- Hamburger Menu Button -->
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle navigation" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <!-- Navigation Menu -->
        <nav class="header-nav" id="headerNav">
            <ul class="nav-list">
                <?php if (session()->get('isLoggedIn')): ?>
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="<?= site_url('dashboard') ?>" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Academic Management -->
                    <li class="nav-item nav-item-dropdown">
                        <button class="nav-link dropdown-toggle" id="academicDropdown" aria-expanded="false">
                            <i class="fas fa-book-open"></i>
                            <span>Academic</span>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </button>
                        <div class="nav-dropdown" id="academicMenu">
                            <a href="<?= site_url('courses') ?>" class="dropdown-item">
                                <i class="fas fa-book"></i> Courses
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-graduation-cap"></i> Enrollments
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-calendar-alt"></i> Schedule
                            </a>
                        </div>
                    </li>

                    <!-- Grades -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-star"></i>
                            <span>Grades</span>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-file-pdf"></i>
                            <span>Reports</span>
                        </a>
                    </li>

                    <!-- Manage Users (Admin Only) -->
                    <?php if (session()->get('role') === 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?= site_url('users') ?>" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>Manage Users</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (session()->get('role') !== 'admin'): ?>
                    <!-- Notifications (Students & Teachers Only) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-relative" style="display: inline-block;">
                                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;"></span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 360px; max-height: 420px; overflow-y: auto;" id="notificationDropdownMenu">
                            <li><span class="dropdown-item text-muted">Loading...</span></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item nav-item-profile">
                        <button class="nav-link profile-btn" id="profileBtn" aria-label="User profile">
                            <div class="user-avatar">
                                <?= strtoupper(substr(session()->get('name'), 0, 1)) ?>
                            </div>
                            <span class="user-name-short"><?= explode(' ', session()->get('name'))[0] ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="profile-header">
                                <div class="profile-avatar-large">
                                    <?= strtoupper(substr(session()->get('name'), 0, 1)) ?>
                                </div>
                                <div class="profile-info">
                                    <p class="profile-name"><?= session()->get('name') ?></p>
                                    <span class="profile-role role-<?= session()->get('role') ?>">
                                        <?= ucfirst(session()->get('role')) ?>
                                    </span>
                                </div>
                            </div>
                            <hr class="profile-divider">
                            <ul class="profile-menu">
                                <li><a href="#" class="profile-link">
                                    <i class="fas fa-user-circle"></i> My Profile
                                </a></li>
                                <li><a href="#" class="profile-link">
                                    <i class="fas fa-cog"></i> Settings
                                </a></li>
                                <li><a href="#" class="profile-link">
                                    <i class="fas fa-lock"></i> Change Password
                                </a></li>
                            </ul>
                            <hr class="profile-divider">
                            <a href="<?= site_url('logout') ?>" class="logout-link">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a href="<?= site_url('/') ?>" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('login') ?>" class="nav-link nav-link-login">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<style>
    /* ============================================
       PROFESSIONAL HEADER STYLES
       ============================================ */

    .professional-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 4px solid #3498db;
    }

    .header-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 64px;
    }

    /* ============================================
       LOGO & BRANDING
       ============================================ */

    .header-logo {
        flex-shrink: 0;
        margin-right: 2rem;
    }

    .brand-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .brand-link:hover {
        color: #3498db;
        transform: translateY(-2px);
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 6px 14px rgba(52, 152, 219, 0.12);
    }

    .brand-text {
        display: flex;
        flex-direction: column;
    }

    .system-title {
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: white;
    }

    /* ============================================
       NAVIGATION
       ============================================ */

    .header-nav {
        flex: 1;
        margin-left: auto;
    }

    .nav-list {
        display: flex;
        align-items: center;
        gap: 0.2rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: #ecf0f1;
        text-decoration: none;
        padding: 0.6rem 1.1rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        border: none;
        background: none;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .nav-link:hover {
        background-color: rgba(52, 152, 219, 0.15);
        color: #3498db;
        padding-left: 1.2rem;
    }

    .nav-link i {
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }

    .nav-link span {
        display: none;
    }

    /* Show labels on medium and up */
    @media (min-width: 768px) {
        .nav-link span { display: inline-block; }
        .nav-link { padding: 0.5rem 0.9rem; }
    }

    /* Dropdown Toggle */
    .dropdown-toggle {
        position: relative;
    }

    .dropdown-icon {
        font-size: 0.75rem;
        margin-left: 0.3rem;
        transition: transform 0.3s ease;
    }

    .nav-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        margin-top: 0.5rem;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .nav-item-dropdown:hover .nav-dropdown,
    .nav-item-dropdown.active .nav-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .nav-item-dropdown:hover .dropdown-icon,
    .nav-item-dropdown.active .dropdown-icon {
        transform: rotate(180deg);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        color: #2c3e50;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #ecf0f1;
        color: #3498db;
        padding-left: 1.5rem;
    }

    .dropdown-item i {
        width: 18px;
        text-align: center;
    }

    /* ============================================
       HAMBURGER MENU
       ============================================ */

    .hamburger {
        display: none;
        flex-direction: column;
        gap: 5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
    }

    .hamburger-line {
        width: 26px;
        height: 3px;
        background-color: white;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .hamburger.active .hamburger-line:nth-child(1) {
        transform: rotate(45deg) translate(10px, 10px);
    }

    .hamburger.active .hamburger-line:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active .hamburger-line:nth-child(3) {
        transform: rotate(-45deg) translate(8px, -8px);
    }

    /* ============================================
       USER PROFILE DROPDOWN
       ============================================ */

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.95rem;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .user-name-short {
        display: none;
    }

    .profile-btn {
        gap: 0.75rem !important;
    }

    .profile-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        margin-top: 0.75rem;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-15px);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .profile-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .profile-avatar-large {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .profile-name {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .profile-role {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        width: fit-content;
    }

    .role-admin {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .role-teacher {
        background-color: #fef3c7;
        color: #92400e;
    }

    .role-student {
        background-color: #dcfce7;
        color: #166534;
    }

    .profile-divider {
        margin: 0.75rem 0;
        border: none;
        border-top: 1px solid #ecf0f1;
    }

    .profile-menu {
        list-style: none;
        margin: 0;
        padding: 0.5rem 0;
    }

    .profile-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        color: #2c3e50;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .profile-link:hover {
        background-color: #ecf0f1;
        color: #3498db;
        padding-left: 1.5rem;
    }

    .profile-link i {
        width: 18px;
        text-align: center;
        font-size: 1rem;
    }

    .logout-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        color: #e74c3c;
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .logout-link:hover {
        background-color: #fee2e2;
        color: #c0392b;
        padding-left: 1.5rem;
    }

    .logout-link i {
        width: 18px;
        text-align: center;
    }

    .nav-link-login {
        background-color: #3498db;
        border: 2px solid #3498db;
        color: white;
        margin-left: 0.5rem;
    }

    .nav-link-login:hover {
        background-color: #2980b9;
        border-color: #2980b9;
    }

    /* ============================================
       RESPONSIVE DESIGN - TABLET (768px and up)
       ============================================ */

    @media (min-width: 768px) {
        .nav-link span {
            display: inline;
        }

        .user-name-short {
            display: none;
        }
    }

    /* ============================================
       RESPONSIVE DESIGN - MOBILE (under 768px)
       ============================================ */

    @media (max-width: 767px) {
        .hamburger {
            display: flex;
        }

        .header-container {
            padding: 0 1rem;
            height: 65px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        .system-title {
            font-size: 0.95rem;
        }

        .header-logo {
            margin-right: 1rem;
        }

        .header-nav {
            position: absolute;
            top: 65px;
            left: 0;
            right: 0;
            background: #2c3e50;
            border-bottom: 4px solid #3498db;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            max-height: 0;
            overflow: hidden;
        }

        .header-nav.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            max-height: 100vh;
        }

        .nav-list {
            flex-direction: column;
            gap: 0;
            padding: 1rem 0;
            align-items: stretch;
        }

        .nav-item {
            width: 100%;
        }

        .nav-link {
            width: 100%;
            padding: 1rem 1.5rem;
            justify-content: flex-start;
            border-radius: 0;
        }

        .nav-link:hover {
            background-color: rgba(52, 152, 219, 0.2);
            padding-left: 1.7rem;
        }

        .nav-link span {
            display: inline;
        }

        .user-name-short {
            display: none;
        }

        .nav-dropdown {
            position: relative;
            top: 0;
            opacity: 1;
            visibility: visible;
            transform: none;
            background: #34495e;
            box-shadow: none;
            border-radius: 0;
            margin-top: 0;
            margin-left: 1.5rem;
            max-width: 250px;
            border-left: 3px solid #3498db;
        }

        .nav-item-dropdown.active .nav-dropdown {
            display: block;
        }

        .nav-item-dropdown:not(.active) .nav-dropdown {
            display: none;
        }

        .dropdown-item {
            color: #ecf0f1;
        }

        .dropdown-item:hover {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .profile-dropdown {
            position: fixed;
            top: 65px;
            left: 0;
            right: 0;
            min-width: 100%;
            border-radius: 0;
            max-height: calc(100vh - 65px);
            overflow-y: auto;
            margin-top: 0;
        }

        .profile-header {
            padding: 1.25rem;
        }

        .profile-avatar-large {
            width: 45px;
            height: 45px;
            font-size: 1.1rem;
        }

        .profile-name {
            font-size: 0.9rem;
        }
    }

    /* ============================================
       SMALL MOBILE (under 480px)
       ============================================ */

    @media (max-width: 479px) {
        .header-container {
            padding: 0 0.75rem;
        }

        .header-logo {
            margin-right: 0.5rem;
        }

        .logo-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }

        .system-title {
            font-size: 0.85rem;
        }

        .nav-link {
            padding: 0.9rem 1.25rem;
            font-size: 0.9rem;
        }

        .nav-link i {
            width: 18px;
        }
    }

    /* ============================================
       UTILITY CLASSES
       ============================================ */

    * {
        box-sizing: border-box;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const headerNav = document.getElementById('headerNav');
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const academicDropdown = document.getElementById('academicDropdown');

        // Hamburger Menu Toggle
        hamburgerBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            hamburgerBtn.classList.toggle('active');
            headerNav.classList.toggle('active');
        });

        // Close menu when link is clicked
        document.querySelectorAll('.nav-link:not(.dropdown-toggle):not(.profile-btn)').forEach(link => {
            link.addEventListener('click', function() {
                hamburgerBtn?.classList.remove('active');
                headerNav?.classList.remove('active');
            });
        });

        // Academic Dropdown Toggle (Mobile)
        academicDropdown?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const parent = this.parentElement;
            parent.classList.toggle('active');
        });

        // Profile Dropdown Toggle
        profileBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown?.classList.toggle('active');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item-profile')) {
                profileDropdown?.classList.remove('active');
            }
        });

        // Prevent closing when clicking inside dropdowns
        profileDropdown?.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header-container')) {
                hamburgerBtn?.classList.remove('active');
                headerNav?.classList.remove('active');
            }
        });
    });
</script>
