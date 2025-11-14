<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Get the count of unread notifications for a user
     * 
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Get the latest notifications for a user (limit 5)
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getNotificationsForUser($userId, $limit = 5)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Mark a specific notification as read
     * 
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }

    /**
     * Create a new notification for a user
     * 
     * @param int $userId
     * @param string $message
     * @return bool|int
     */
    public function createNotification($userId, $message)
    {
        return $this->insert([
            'user_id' => $userId,
            'message' => $message,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get all unread notifications for a user
     * 
     * @param int $userId
     * @return array
     */
    public function getUnreadNotifications($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
