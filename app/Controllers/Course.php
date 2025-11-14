<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Course extends BaseController
{
    protected $helpers = ['url'];

    public function enroll()
    {
        // Check if logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response
                ->setJSON([
                    'success' => false,
                    'message' => 'User not logged in'
                ])
                ->setStatusCode(401);
        }

        // Get course ID
        $courseId = $this->request->getPost('course_id');
        if (!$courseId) {
            return $this->response
                ->setJSON([
                    'success' => false,
                    'message' => 'Course ID is required'
                ])
                ->setStatusCode(400);
        }

        $userId = $session->get('user_id');
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $courseModel = new \App\Models\CourseModel();
        $notificationModel = new NotificationModel();

        // Check if already enrolled
        $existingEnrollment = $enrollmentModel
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
            
        if ($existingEnrollment) {
            return $this->response
                ->setJSON([
                    'success' => false,
                    'message' => 'Already enrolled in this course'
                ]);
        }

        // Get course information
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response
                ->setJSON([
                    'success' => false,
                    'message' => 'Course not found'
                ])
                ->setStatusCode(404);
        }

        // Insert new enrollment
        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s'),
        ];

        try {
            if ($enrollmentModel->insert($data)) {
                // Create a notification for the student
                $courseName = $course['title'] ?? $course['name'] ?? 'Unknown Course';
                $message = "You have been successfully enrolled in the course: <strong>$courseName</strong>";
                $notificationModel->createNotification($userId, $message);

                return $this->response
                    ->setJSON([
                        'success' => true,
                        'message' => 'Successfully enrolled in this course!'
                    ]);
            } else {
                return $this->response
                    ->setJSON([
                        'success' => false,
                        'message' => 'Failed to save enrollment'
                    ])
                    ->setStatusCode(500);
            }
        } catch (\Exception $e) {
            return $this->response
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ])
                ->setStatusCode(500);
        }
    }
}
