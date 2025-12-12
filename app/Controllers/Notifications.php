<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get unread notifications for the current user
     * Returns JSON response with unread count and list of unread notifications
     */
    public function get()
    {
        // Check if user is logged in
        $session = service('session');
        
        if (!$session->get('isLoggedIn')) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'success' => false,
                    'message' => 'User not logged in',
                    'csrfHash' => csrf_hash(),
                    'unreadCount' => 0,
                    'notifications' => []
                ]);
        }

        $userId = $session->get('user_id');
        
        try {
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            $notifications = $this->notificationModel->getNotificationsForUser($userId, 5);

            return $this->response->setJSON([
                'success' => true,
                'csrfHash' => csrf_hash(),
                'unreadCount' => $unreadCount,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON([
                    'success' => false,
                    'message' => 'Error fetching notifications',
                    'csrfHash' => csrf_hash(),
                    'unreadCount' => 0,
                    'notifications' => []
                ]);
        }
    }

    /**
     * Mark a specific notification as read
     * Accepts notification ID as parameter
     */
    public function mark_as_read($id = null)
    {
        // Check if user is logged in
        $session = service('session');
        
        if (!$session->get('isLoggedIn')) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'success' => false,
                    'message' => 'User not logged in',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        if (!$id) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON([
                    'success' => false,
                    'message' => 'Notification ID is required',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        try {
            // Verify the notification belongs to the user
            $notification = $this->notificationModel->find($id);
            
            if (!$notification) {
                return $this->response
                    ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Notification not found',
                        'csrfHash' => csrf_hash(),
                    ]);
            }

            if ($notification['user_id'] != $session->get('user_id')) {
                return $this->response
                    ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Unauthorized access to this notification',
                        'csrfHash' => csrf_hash(),
                    ]);
            }

            // Mark as read
            $this->notificationModel->markAsRead($id);
            
            // Get updated unread count
            $userId = $session->get('user_id');
            $unreadCount = $this->notificationModel->getUnreadCount($userId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read',
                'csrfHash' => csrf_hash(),
                'unreadCount' => $unreadCount
            ]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON([
                    'success' => false,
                    'message' => 'Error marking notification as read',
                    'csrfHash' => csrf_hash(),
                ]);
        }
    }
}
