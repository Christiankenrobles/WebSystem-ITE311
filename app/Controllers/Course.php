<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\CourseModel;
use App\Models\UserModel;

class Course extends BaseController
{
    protected $helpers = ['url'];

    public function index()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Please login first');
        }

        // Get search term if provided
        $searchTerm = $this->request->getVar('q') ?? '';
        $searchTerm = trim($searchTerm);

        $courseModel = new \App\Models\CourseModel();

        if (!empty($searchTerm)) {
            // Perform search
            $courses = $courseModel->where('title LIKE', "%$searchTerm%")
                                    ->orWhere('description LIKE', "%$searchTerm%")
                                    ->findAll();
        } else {
            // Get all courses
            $courses = $courseModel->findAll();
        }

        $data = [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
            'title' => 'Course Catalog'
        ];

        return view('courses', $data);
    }

    public function search()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'User not logged in'
                ]);
        }

        // Get search term from GET or POST
        $searchTerm = $this->request->getVar('q') ?? $this->request->getVar('search');
        $searchTerm = trim($searchTerm ?? '');

        // Validate search term
        if (empty($searchTerm)) {
            // Return all courses if no search term
            $courseModel = new \App\Models\CourseModel();
            $results = $courseModel->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'results' => $results,
                'total' => count($results),
                'message' => 'No search term provided'
            ]);
        }

        try {
            $courseModel = new \App\Models\CourseModel();
            
            // Search using LIKE queries on title and description
            $results = $courseModel->where('title LIKE', "%$searchTerm%")
                                   ->orWhere('description LIKE', "%$searchTerm%")
                                   ->findAll();

            // Check if this is an AJAX request or regular request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'results' => $results,
                    'total' => count($results),
                    'searchTerm' => htmlspecialchars($searchTerm),
                    'message' => count($results) > 0 ? 'Search completed successfully' : 'No courses found matching your search'
                ]);
            } else {
                // Regular request - return view
                $data = [
                    'courses' => $results,
                    'searchTerm' => $searchTerm,
                    'totalResults' => count($results)
                ];
                
                return view('search_results', $data);
            }
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Error performing search: ' . $e->getMessage(),
                    'results' => []
                ]);
        }
    }

    /**
     * Create a new course (accepts JSON or form data)
     * Expected fields: course_name, course_code, description, academic_year, semester, term,
     * schedule, assigned_teacher (name or id), status
     */
    public function create()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'User not logged in',
            ]);
        }

        // Only allow teachers or admins to create courses
        $role = $session->get('role');
        if (!in_array($role, ['admin', 'teacher'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Insufficient permissions to create courses'
            ]);
        }

        // Accept JSON payload or fallback to POST
        $input = $this->request->getJSON(true);
        if (empty($input) || !is_array($input)) {
            $input = $this->request->getPost();
        }

        $courseName = isset($input['course_name']) ? trim($input['course_name']) : '';
        $courseCode = isset($input['course_code']) ? trim($input['course_code']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';

        if (empty($courseName) || empty($courseCode)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Course name and code are required'
            ]);
        }

        // Try to resolve assigned teacher to an ID
        $assignedTeacher = $input['assigned_teacher'] ?? null;
        $instructorId = null;
        if (!empty($assignedTeacher)) {
            $userModel = new UserModel();

            // If numeric, treat as user id
            if (is_numeric($assignedTeacher)) {
                $possible = $userModel->find((int)$assignedTeacher);
                if ($possible) {
                    $instructorId = $possible['id'];
                }
            } else {
                // Try to find by exact name first, then by email if looks like an email
                if (filter_var($assignedTeacher, FILTER_VALIDATE_EMAIL)) {
                    $possible = $userModel->where('email', $assignedTeacher)->first();
                    if ($possible) $instructorId = $possible['id'];
                } else {
                    $possible = $userModel->like('name', $assignedTeacher)->first();
                    if ($possible) $instructorId = $possible['id'];
                }
            }
        }

        $courseModel = new CourseModel();

        $data = [
            'title' => $courseName,
            'description' => $description,
            'instructor_id' => $instructorId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $insertId = $courseModel->insert($data);
            if ($insertId === false) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to insert course',
                    'errors' => $courseModel->errors()
                ]);
            }

            $created = $courseModel->find($insertId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course created',
                'course' => $created
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error creating course: ' . $e->getMessage()
            ]);
        }
    }

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
