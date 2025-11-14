<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Add test notifications for user ID 2 (John Student)
        $data = [
            [
                'user_id' => 2,
                'message' => 'Welcome! You have been enrolled in the course: <strong>Introduction to PHP</strong>',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            ],
            [
                'user_id' => 2,
                'message' => 'New course material uploaded for Web Development course',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'user_id' => 2,
                'message' => 'Your assignment has been graded. Check your materials for feedback.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
            ],
        ];

        $this->db->table('notifications')->insertBatch($data);
    }
}
