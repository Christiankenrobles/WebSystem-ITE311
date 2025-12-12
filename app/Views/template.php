<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'MyApp' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        :root {
            --app-bg: #f6f8fb;
            --app-bg-2: #eef2ff;
            --app-text: #243142;
            --app-muted: #6b7280;
            --app-card: #ffffff;
            --app-border: rgba(17, 24, 39, 0.08);
            --app-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
            --app-shadow-sm: 0 6px 18px rgba(17, 24, 39, 0.08);
        }

        body {
            background: radial-gradient(1000px 500px at 15% 0%, var(--app-bg-2) 0%, transparent 60%), var(--app-bg);
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            color: var(--app-text);
        }

        .app-main {
            max-width: 1200px;
            padding-top: 1.25rem;
            padding-bottom: 3rem;
        }

        .card {
            border-radius: 14px;
            border: 1px solid var(--app-border) !important;
            background: var(--app-card);
            box-shadow: var(--app-shadow-sm);
        }

        .card.shadow-sm {
            box-shadow: var(--app-shadow-sm) !important;
        }

        .card:hover {
            transform: translateY(-1px);
            transition: transform 120ms ease, box-shadow 120ms ease;
            box-shadow: var(--app-shadow);
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-lg {
            border-radius: 14px;
        }

        .btn-outline-danger,
        .btn-outline-primary,
        .btn-outline-secondary {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(6px);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid var(--app-border);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(59, 130, 246, 0.55);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
        }

        .badge {
            border-radius: 999px;
        }

        .text-muted {
            color: var(--app-muted) !important;
        }

        .alert {
            border-radius: 14px;
            border: 1px solid var(--app-border);
            box-shadow: var(--app-shadow-sm);
        }

        .table {
            --bs-table-bg: transparent;
        }

        .table thead th {
            color: var(--app-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
        }
    </style>
</head>
<body>
    <!-- Modern Header Component -->
    <?= $this->include('components/header') ?>
    <!-- Page Content -->
    <main class="app-main container mt-4">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <!-- Notification Script -->
    <script>
        // Keep CSRF token in sync for AJAX requests
        function setCsrfHash(newHash) {
            if (newHash) {
                $('meta[name="csrf-token"]').attr('content', newHash);
            }
        }

        function getCsrfHash() {
            return $('meta[name="csrf-token"]').attr('content');
        }

        function getCsrfTokenName() {
            return $('meta[name="csrf-token-name"]').attr('content');
        }

        // Add CSRF header for all AJAX requests (CodeIgniter supports X-CSRF-TOKEN)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': getCsrfHash()
            }
        });

        // Function to fetch and update notifications
        function fetchNotifications() {
            $.get('<?= site_url('/notifications') ?>', function(data) {
                if (data.success) {
                    setCsrfHash(data.csrfHash);
                    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': getCsrfHash() } });
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
                                    <div class="dropdown-item px-3 py-2 alert ${alertClass} mb-1" role="alert" data-notification-id="${notification.id}" data-is-read="${notification.is_read}">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div style="flex: 1; min-width: 0;">
                                                <p class="mb-1" style="white-space: normal;">${notification.message}</p>
                                                <small class="text-muted d-block">${formattedDate}</small>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary mark-as-read-btn" data-id="${notification.id}" style="display: ${notification.is_read === 1 ? 'none' : 'inline-block'}">
                                                    Mark as Read
                                                </button>
                                            </div>
                                        </div>
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
                if (data && data.csrfHash) {
                    setCsrfHash(data.csrfHash);
                    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': getCsrfHash() } });
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

                const csrfData = {};
                csrfData[getCsrfTokenName()] = getCsrfHash();

                $.post('<?= site_url('/notifications/mark_read') ?>/' + notificationId, csrfData, function(data) {
                    if (data.success) {
                        setCsrfHash(data.csrfHash);
                        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': getCsrfHash() } });
                        // Hide the button but keep the notification visible
                        const container = button.closest('.alert');
                        container.removeClass('alert-info').addClass('alert-light');
                        container.attr('data-is-read', '1');
                        button.hide();

                        // Update badge count without removing notifications
                        if (typeof data.unreadCount !== 'undefined') {
                            const badge = $('#notificationBadge');
                            if (data.unreadCount > 0) {
                                badge.text(data.unreadCount).show();
                            } else {
                                badge.hide();
                            }
                        } else {
                            fetchNotifications();
                        }
                    }
                    if (data && data.csrfHash) {
                        setCsrfHash(data.csrfHash);
                        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': getCsrfHash() } });
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
