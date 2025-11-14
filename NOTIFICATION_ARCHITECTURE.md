# Notification System - Architecture & Quick Reference

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    User Interface (Browser)                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │           Bootstrap Navbar with Notifications         │   │
│  │  ┌────────────────────────────────────────────────┐  │   │
│  │  │ Home | About | Contact | [Bell Icon][Badge:3] │  │   │
│  │  └────────────────────────────────────────────────┘  │   │
│  │                                                        │   │
│  │  ┌───── Notification Dropdown (on click) ─────┐      │   │
│  │  │ • You have been enrolled in PHP        ✓    │      │   │
│  │  │ • New material uploaded                [OK] │      │   │
│  │  │ • Assignment graded                    [OK] │      │   │
│  │  └────────────────────────────────────────────┘      │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕ (AJAX)
┌─────────────────────────────────────────────────────────────┐
│                   JavaScript/jQuery Layer                   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ fetchNotifications()                                 │   │
│  │ - Called on page load                                │   │
│  │ - Auto-refresh every 30 seconds                      │   │
│  │ - Updates badge and dropdown                         │   │
│  │                                                      │   │
│  │ markAsRead(id)                                       │   │
│  │ - POST request to mark notification as read          │   │
│  │ - Updates display with smooth animation             │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕ (HTTP)
┌─────────────────────────────────────────────────────────────┐
│              API Routes (app/Config/Routes.php)            │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ GET  /notifications                                  │   │
│  │ POST /notifications/mark_read/{id}                   │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│        Notifications Controller (API Endpoints)             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ - Authentication check                               │   │
│  │ - Authorization verification                         │   │
│  │ - JSON response formatting                           │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│          NotificationModel (Data Access Layer)              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ - getUnreadCount($userId)                            │   │
│  │ - getNotificationsForUser($userId)                   │   │
│  │ - markAsRead($notificationId)                        │   │
│  │ - createNotification($userId, $message)              │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│              MySQL Database (notifications table)           │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ id | user_id | message | is_read | created_at       │   │
│  │ 1  | 2       | Msg...  | 0       | 2025-11-14...    │   │
│  │ 2  | 2       | Msg...  | 0       | 2025-11-14...    │   │
│  │ 3  | 2       | Msg...  | 1       | 2025-11-14...    │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow

### 1. Page Load Flow
```
User Logs In
    ↓
Redirected to Dashboard
    ↓
template.php loaded
    ↓
JavaScript $(document).ready() executed
    ↓
fetchNotifications() called via $.get()
    ↓
GET /notifications endpoint
    ↓
Notifications::get() controller method
    ↓
NotificationModel queries database
    ↓
JSON response with unread count and notifications
    ↓
jQuery updates badge and dropdown
    ↓
Badge shows "3" if 3 unread notifications
```

### 2. Mark as Read Flow
```
User clicks "Mark as Read" button
    ↓
jQuery event handler triggered
    ↓
$.post() request to /notifications/mark_read/{id}
    ↓
Notifications::mark_as_read() controller method
    ↓
Verify notification ownership
    ↓
NotificationModel->markAsRead() updates database
    ↓
JSON response with updated unread count
    ↓
jQuery removes notification from dropdown
    ↓
Badge count decreases
```

### 3. Enrollment Flow
```
Student clicks "Enroll" button
    ↓
$.post() to /course/enroll
    ↓
Course::enroll() controller method
    ↓
Enrollment record inserted
    ↓
NotificationModel->createNotification() called
    ↓
Notification record inserted with enrollment message
    ↓
JSON success response sent to browser
    ↓
Auto-refresh (30s) will pick up new notification
    ↓
Badge updates automatically
```

## Key Files Quick Reference

| File | Purpose | Key Methods/Functions |
|------|---------|----------------------|
| `app/Models/NotificationModel.php` | Database operations | getUnreadCount, getNotificationsForUser, markAsRead, createNotification |
| `app/Controllers/Notifications.php` | API endpoints | get(), mark_as_read() |
| `app/Controllers/Course.php` | Modified for notifications | enroll() - now creates notifications |
| `app/Views/template.php` | UI and JavaScript | fetchNotifications(), attachMarkAsReadHandlers() |
| `app/Config/Routes.php` | API routes | /notifications, /notifications/mark_read/{id} |
| `app/Database/Migrations/2025-11-14-044432_CreateNotificationsTable.php` | Table schema | Table structure with fields |

## Database Schema

```sql
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at DATETIME,
    KEY user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);
```

## API Endpoints Reference

### GET /notifications
**Purpose:** Fetch user's notifications
**Returns:** JSON
```json
{
  "success": true,
  "unreadCount": 3,
  "notifications": [
    {
      "id": 1,
      "user_id": 2,
      "message": "You have been enrolled...",
      "is_read": 0,
      "created_at": "2025-11-14 10:00:00"
    }
  ]
}
```

### POST /notifications/mark_read/{id}
**Purpose:** Mark notification as read
**Returns:** JSON
```json
{
  "success": true,
  "message": "Notification marked as read",
  "unreadCount": 2
}
```

## JavaScript Functions Reference

### fetchNotifications()
- Makes AJAX GET request to /notifications
- Updates badge count in navbar
- Populates dropdown with notification list
- Attaches click handlers to mark as read buttons
- Called on page load and every 30 seconds

### attachMarkAsReadHandlers()
- Attaches click event to all "Mark as Read" buttons
- Makes POST request to /notifications/mark_read/{id}
- Removes notification from dropdown with fade animation
- Calls fetchNotifications() to update counts

### Auto-refresh Interval
```javascript
setInterval(fetchNotifications, 30000); // Every 30 seconds
```

## Testing Endpoints

### Test in Browser Console
```javascript
// Get current notifications
$.get('/notifications', function(data) { console.log(data); });

// Mark notification with ID 1 as read
$.post('/notifications/mark_read/1', function(data) { console.log(data); });
```

### Test with cURL
```bash
# Get notifications
curl -b cookies.txt http://localhost:8080/notifications

# Mark as read
curl -X POST -b cookies.txt http://localhost:8080/notifications/mark_read/1
```

## Performance Considerations

- **Auto-refresh interval:** 30 seconds (configurable in template.php)
- **Notification limit:** 10 per fetch (configurable in Notifications controller)
- **Database indices:** user_id field indexed for fast queries
- **AJAX caching:** Disabled to ensure fresh data
- **Bundle size:** Bootstrap 5 CDN, jQuery 3.6.0 CDN, Font Awesome 6.4.0 CDN

## Security Features

✓ User authentication required (session check)
✓ User authorization (can only access own notifications)
✓ CSRF token included in AJAX requests
✓ SQL injection prevention (using parameterized queries)
✓ XSS protection (proper HTML escaping in messages)
✓ Proper HTTP status codes for errors
✓ Error messages don't reveal system details

## Customization Guide

### Change Auto-refresh Interval
Edit `app/Views/template.php`, find:
```javascript
setInterval(fetchNotifications, 30000); // Change 30000 to desired milliseconds
```

### Change Notification Limit
Edit `app/Controllers/Notifications.php`, find:
```php
$notifications = $this->notificationModel->getNotificationsForUser($userId, 10); // Change 10
```

### Modify Badge Styling
Edit `app/Views/template.php`, find:
```html
<span class="badge bg-danger position-absolute..."> <!-- Change bg-danger to other Bootstrap colors -->
```

### Add Notification Types/Categories
Add a `type` column to notifications table:
```php
'type' => ['type' => 'VARCHAR', 'constraint' => 50] // enrollment, material, grade, etc.
```

## Troubleshooting Guide

| Issue | Solution |
|-------|----------|
| Notifications not showing | Check browser console (F12), verify login session, check /notifications endpoint |
| Badge not updating | Clear browser cache, check 30-second interval, verify JavaScript console |
| Mark as Read not working | Check POST request in Network tab, verify notification ID, check permissions |
| AJAX errors | Check CORS headers, verify routes are correct, check authentication status |
| Database errors | Run migration: `php spark migrate`, verify table exists with correct schema |

---
**Last Updated:** November 14, 2025
**System Status:** ✅ Fully Operational
