<?php

namespace App\Controllers;

class Course extends BaseController
{
    public function enroll()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in'])->setStatusCode(401);
        }

        $courseId = $this->request->getPost('course_id');
        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID is required'])->setStatusCode(400);
        }

        $userId = $session->get('user_id');
        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Check if already enrolled
        $existingEnrollment = $enrollmentModel->where('user_id', $userId)->where('course_id', $courseId)->first();
        if ($existingEnrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Already enrolled in this course']);
        }

        // Insert new enrollment
        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s'),
        ];

        if ($enrollmentModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Enrolled successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment failed'])->setStatusCode(500);
        }
    }
}
