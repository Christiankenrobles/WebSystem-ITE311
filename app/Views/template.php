<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'MyApp' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('/') ?>">MyApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('contact') ?>">Contact</a>
                    </li>
                    <?php if (session()->get('isLoggedIn')): ?>
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="notificationBadge" style="display: none;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notificationDropdownMenu">
                            <li><a class="dropdown-item text-muted" href="#">No notifications</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mt-5">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <!-- Notification Script -->
    <script>
        // Function to fetch and update notifications
        function fetchNotifications() {
            $.get('<?= site_url('/notifications') ?>', function(data) {
                if (data.success) {
                    const unreadCount = data.unreadCount;
                    const notifications = data.notifications;
                    const badge = $('#notificationBadge');
                    const dropdown = $('#notificationDropdownMenu');

                    // Update badge
                    if (unreadCount > 0) {
                        badge.text(unreadCount).show();
                    } else {
                        badge.hide();
                    }

                    // Update dropdown menu
                    if (notifications.length > 0) {
                        let html = '';
                        notifications.forEach(function(notification) {
                            const formattedDate = new Date(notification.created_at).toLocaleString();
                            const alertClass = notification.is_read === 1 ? 'alert-light' : 'alert-info';
                            html += `
                                <li>
                                    <div class="dropdown-item px-3 py-2 alert ${alertClass} mb-1" role="alert">
                                        <p class="mb-2">${notification.message}</p>
                                        <small class="text-muted d-block mb-2">${formattedDate}</small>
                                        <button class="btn btn-sm btn-outline-primary mark-as-read-btn" data-id="${notification.id}" style="display: ${notification.is_read === 1 ? 'none' : 'inline-block'}">
                                            Mark as Read
                                        </button>
                                    </div>
                                </li>
                            `;
                        });
                        dropdown.html(html);

                        // Attach click handler to mark as read buttons
                        attachMarkAsReadHandlers();
                    } else {
                        dropdown.html('<li><a class="dropdown-item text-muted" href="#">No notifications</a></li>');
                    }
                }
            });
        }

        // Function to mark notification as read
        function attachMarkAsReadHandlers() {
            $('.mark-as-read-btn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const notificationId = $(this).data('id');
                const button = $(this);

                $.post('<?= site_url('/notifications/mark_read') ?>/' + notificationId, function(data) {
                    if (data.success) {
                        // Hide the button and refresh notifications
                        button.closest('.alert').fadeOut(300, function() {
                            $(this).remove();
                            fetchNotifications(); // Refresh to update count and display
                        });
                    }
                });
            });
        }

        // Load notifications when page is ready
        $(document).ready(function() {
            fetchNotifications();
            
            // Optional: Refresh notifications every 30 seconds
            setInterval(fetchNotifications, 30000);
        });
    </script>
</body>
</html>
