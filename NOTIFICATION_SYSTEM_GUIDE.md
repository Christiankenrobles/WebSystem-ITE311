# Notification System - Testing Guide

## Overview
The notification system has been successfully implemented with the following features:
- Database migrations for notifications table
- NotificationModel with methods for retrieving and managing notifications
- Notifications API controller with JSON endpoints
- Bootstrap UI with notification dropdown and badge
- jQuery/AJAX integration for real-time notification fetching
- Automatic notification creation on course enrollment

## Development Server
The development server is running on: **http://localhost:8080**

## Test Credentials
Use the following student account to test notifications:
- **Email:** john.student@example.com
- **Password:** password
- **Role:** Student
- **User ID:** 2

## Testing Steps

### Step 1: View Pre-loaded Test Notifications
1. Navigate to http://localhost:8080/login
2. Log in with student credentials (john.student@example.com / password)
3. After login, you should be redirected to the dashboard

### Step 2: View Notification Badge and Dropdown
1. Look at the top right corner of the navbar
2. You should see a **bell icon** with a **red badge showing "3"** (for the 3 unread notifications)
3. Click on the bell icon to open the notification dropdown
4. You should see 3 notification items displayed:
   - "Welcome! You have been enrolled in the course: Introduction to PHP"
   - "New course material uploaded for Web Development course"
   - "Your assignment has been graded. Check your materials for feedback."

### Step 3: Test Mark as Read Functionality
1. In the notification dropdown, locate a notification with a "Mark as Read" button
2. Click the "Mark as Read" button
3. The notification should fade out and be removed from the list
4. The badge count should decrease (e.g., from 3 to 2)
5. Repeat for other notifications to test multiple mark-as-read actions

### Step 4: Test Automatic Badge Update
1. After marking all notifications as read:
   - The badge should disappear or show "0"
   - The dropdown should display "No notifications"

### Step 5: Test Auto-Refresh (Optional)
1. Open the browser's Developer Tools (F12)
2. Open the Console tab
3. Every 30 seconds, you should see the notifications fetch happening automatically
4. Open another tab/window and manually update the `is_read` field in the database
5. Return to the original tab and wait for the 30-second refresh - changes should appear

### Step 6: Test Course Enrollment Notification (Optional)
1. Navigate to the dashboard (if not already there)
2. If you see "Available Courses" section, click the "Enroll" button on any course
3. The enrollment should succeed without page reload (AJAX)
4. Check the notification dropdown - a new notification should appear:
   - "You have been successfully enrolled in the course: [Course Name]"
5. The badge should update to show the new unread count

## API Endpoints

### Get Notifications
**Endpoint:** `GET /notifications`
**Response:** JSON with unread count and notification list
```json
{
  "success": true,
  "unreadCount": 3,
  "notifications": [
    {
      "id": 1,
      "user_id": 2,
      "message": "Welcome! You have been enrolled in the course: Introduction to PHP",
      "is_read": 0,
      "created_at": "2025-11-14 10:00:00"
    },
    ...
  ]
}
```

### Mark Notification as Read
**Endpoint:** `POST /notifications/mark_read/{id}`
**Response:** JSON with success status and updated unread count
```json
{
  "success": true,
  "message": "Notification marked as read",
  "unreadCount": 2
}
```

## Files Created/Modified

### New Files
- `app/Controllers/Notifications.php` - API controller for notifications
- `app/Models/NotificationModel.php` - Model for notification operations
- `app/Database/Migrations/2025-11-14-044432_CreateNotificationsTable.php` - Database migration
- `app/Database/Seeds/NotificationSeeder.php` - Seeder for test data

### Modified Files
- `app/Controllers/BaseController.php` - Added NotificationModel initialization
- `app/Controllers/Course.php` - Added notification creation on enrollment
- `app/Config/Routes.php` - Added notification API routes
- `app/Views/template.php` - Added notification dropdown UI and JavaScript

## Database Schema

### notifications table
| Field | Type | Null | Default | Key |
|-------|------|------|---------|-----|
| id | INT | No | AUTO_INCREMENT | PK |
| user_id | INT | No | | FK |
| message | TEXT | No | | |
| is_read | TINYINT | No | 0 | |
| created_at | DATETIME | Yes | | |

## Key Features

1. **Real-time Updates:** Notifications are fetched every 30 seconds automatically
2. **Unread Count:** Badge displays number of unread notifications
3. **Mark as Read:** Each notification has a "Mark as Read" button for quick management
4. **Security:** Notifications are only accessible to the logged-in user
5. **Enrollment Integration:** Notifications are automatically created when students enroll in courses
6. **Responsive Design:** Works on all screen sizes with Bootstrap 5
7. **Clean UI:** Uses Font Awesome icons and Bootstrap alerts for styling

## Troubleshooting

### Badge not appearing
- Ensure you're logged in
- Check browser console for JavaScript errors (F12 > Console)
- Verify `/notifications` endpoint returns correct data (Network tab)

### Notifications not loading
- Check that the notifications table exists: `php spark db:table notifications`
- Verify test data was seeded: `php spark db:seed NotificationSeeder`
- Check PHP error logs in `writable/logs/`

### Mark as Read not working
- Ensure the notification belongs to the logged-in user
- Check browser console for errors
- Verify POST endpoint `/notifications/mark_read/{id}` is accessible

## Next Steps

After testing, you can:
1. Clean up test data by clearing the notifications table
2. Integrate notifications for other user actions (e.g., assignments, quiz results)
3. Add notification categories/types for better organization
4. Implement email notifications as a complementary feature
5. Add notification preferences for users
