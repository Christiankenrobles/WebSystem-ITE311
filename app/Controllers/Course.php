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

    /**
     * Display enrollment schedule for the logged-in user
     */
    public function schedule()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Please login first');
        }

        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        $courseModel = new CourseModel();

        // Get enrollment schedule
        $enrollments = $courseModel->getEnrollmentSchedule($userId);

        // Group by day for better display
        $scheduleByDay = [];
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($enrollments as $enrollment) {
            $scheduleDays = $enrollment['schedule_days'] ?? '';
            if (!empty($scheduleDays)) {
                // Handle different formats: "Monday,Wednesday" or "MWF" or "Mon,Wed"
                $days = explode(',', $scheduleDays);
                foreach ($days as $day) {
                    $day = trim($day);
                    // Map abbreviations to full day names
                    $dayMap = [
                        'M' => 'Monday', 'Mon' => 'Monday',
                        'T' => 'Tuesday', 'Tue' => 'Tuesday',
                        'W' => 'Wednesday', 'Wed' => 'Wednesday',
                        'TH' => 'Thursday', 'Thu' => 'Thursday',
                        'F' => 'Friday', 'Fri' => 'Friday',
                        'S' => 'Saturday', 'Sat' => 'Saturday',
                        'SU' => 'Sunday', 'Sun' => 'Sunday'
                    ];
                    $fullDay = $dayMap[strtoupper($day)] ?? $day;
                    if (!isset($scheduleByDay[$fullDay])) {
                        $scheduleByDay[$fullDay] = [];
                    }
                    $scheduleByDay[$fullDay][] = $enrollment;
                }
            } else {
                // No schedule set
                if (!isset($scheduleByDay['Unscheduled'])) {
                    $scheduleByDay['Unscheduled'] = [];
                }
                $scheduleByDay['Unscheduled'][] = $enrollment;
            }
        }

        // Sort days
        $sortedSchedule = [];
        foreach ($daysOfWeek as $day) {
            if (isset($scheduleByDay[$day])) {
                // Sort by time
                usort($scheduleByDay[$day], function($a, $b) {
                    $timeA = $a['schedule_time_start'] ?? '00:00:00';
                    $timeB = $b['schedule_time_start'] ?? '00:00:00';
                    return strcmp($timeA, $timeB);
                });
                $sortedSchedule[$day] = $scheduleByDay[$day];
            }
        }
        // Add unscheduled at the end
        if (isset($scheduleByDay['Unscheduled'])) {
            $sortedSchedule['Unscheduled'] = $scheduleByDay['Unscheduled'];
        }

        $data = [
            'enrollments' => $enrollments,
            'scheduleByDay' => $sortedSchedule,
            'daysOfWeek' => $daysOfWeek,
            'userRole' => $userRole,
            'title' => 'My Enrollment Schedule'
        ];

        return view('enrollment_schedule', $data);
    }

    /**
     * Edit schedule for a course
     */
    public function editSchedule($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Please login first');
        }

        $userRole = $session->get('role');
        // Only admin and teachers can edit schedules
        if (!in_array($userRole, ['admin', 'teacher'])) {
            return redirect()->to('dashboard')->with('error', 'You do not have permission to edit schedules');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return redirect()->to('dashboard')->with('error', 'Course not found');
        }

        // If teacher, check if they own this course
        if ($userRole === 'teacher') {
            $userId = $session->get('user_id');
            if ($course['instructor_id'] != $userId) {
                return redirect()->to('dashboard')->with('error', 'You can only edit schedules for your own courses');
            }
        }

        $data = [
            'course' => $course,
            'title' => 'Edit Schedule - ' . $course['title']
        ];

        return view('edit_schedule', $data);
    }

    /**
     * Update schedule for a course
     */
    public function updateSchedule($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }

        $userRole = $session->get('role');
        if (!in_array($userRole, ['admin', 'teacher'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You do not have permission to update schedules'
            ]);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Course not found'
            ]);
        }

        // If teacher, check if they own this course
        if ($userRole === 'teacher') {
            $userId = $session->get('user_id');
            if ($course['instructor_id'] != $userId) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'You can only update schedules for your own courses'
                ]);
            }
        }

        // Get schedule data from POST
        $scheduleDays = $this->request->getPost('schedule_days');
        $scheduleTimeStart = $this->request->getPost('schedule_time_start');
        $scheduleTimeEnd = $this->request->getPost('schedule_time_end');
        $scheduleLocation = $this->request->getPost('schedule_location');
        $scheduleType = $this->request->getPost('schedule_type');

        // Prepare update data
        $updateData = [
            'schedule_days' => $scheduleDays ?? null,
            'schedule_time_start' => $scheduleTimeStart ?? null,
            'schedule_time_end' => $scheduleTimeEnd ?? null,
            'schedule_location' => $scheduleLocation ?? null,
            'schedule_type' => $scheduleType ?? 'regular',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($courseModel->update($courseId, $updateData)) {
                // If AJAX request, return JSON
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Schedule updated successfully',
                        'course' => $courseModel->find($courseId)
                    ]);
                } else {
                    // Regular request, redirect
                    return redirect()->to('course/schedule/' . $courseId)->with('success', 'Schedule updated successfully');
                }
            } else {
                $errors = $courseModel->errors();
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Failed to update schedule',
                        'errors' => $errors
                    ]);
                } else {
                    return redirect()->back()->withInput()->with('errors', $errors);
                }
            }
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Error updating schedule: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()->with('error', 'Error updating schedule: ' . $e->getMessage());
            }
        }
    }

    /**
     * Upload schedule file (CSV/Excel) for bulk import
     */
    public function uploadSchedule()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Please login first');
        }

        $userRole = $session->get('role');
        // Only admin and teachers can upload schedules
        if (!in_array($userRole, ['admin', 'teacher'])) {
            return redirect()->to('dashboard')->with('error', 'You do not have permission to upload schedules');
        }

        // Handle file upload
        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('schedule_file');
            
            if (!$file->isValid()) {
                return redirect()->back()->withInput()->with('error', 'No file uploaded or file is invalid');
            }

            // Validate file type
            $allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain'];
            $fileExtension = $file->getClientExtension();
            
            if (!in_array($fileExtension, ['csv', 'xlsx', 'xls', 'txt']) && !in_array($file->getClientMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()->with('error', 'Invalid file type. Please upload CSV or Excel file.');
            }

            // Configure upload
            $uploadPath = WRITEPATH . 'uploads/schedules/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            if (!$file->hasMoved()) {
                $file->move($uploadPath, $newName);
            }

            $filePath = $uploadPath . $newName;

            // Parse the file
            $parsedData = $this->parseScheduleFile($filePath, $fileExtension);
            
            if (empty($parsedData)) {
                @unlink($filePath); // Delete uploaded file
                return redirect()->back()->withInput()->with('error', 'Failed to parse file or file is empty. Please check the file format.');
            }

            // Store parsed data in session for preview
            $session->set('schedule_upload_data', $parsedData);
            $session->set('schedule_upload_file', $newName);

            return redirect()->to('course/preview-schedule-upload')->with('success', 'File uploaded successfully. Please review the data before importing.');
        }

        // Display upload form
        $courseModel = new CourseModel();
        $allCourses = $courseModel->findAll();

        $data = [
            'courses' => $allCourses,
            'title' => 'Upload Schedule File'
        ];

        return view('upload_schedule', $data);
    }

    /**
     * Preview schedule data before importing
     */
    public function previewScheduleUpload()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('login');
        }

        $parsedData = $session->get('schedule_upload_data');
        $fileName = $session->get('schedule_upload_file');

        if (empty($parsedData)) {
            return redirect()->to('course/upload-schedule')->with('error', 'No schedule data found. Please upload a file first.');
        }

        $courseModel = new CourseModel();
        $allCourses = $courseModel->findAll();
        $coursesMap = [];
        foreach ($allCourses as $course) {
            $coursesMap[strtolower(trim($course['title']))] = $course;
        }

        // Match courses and prepare for preview
        $previewData = [];
        $errors = [];
        foreach ($parsedData as $index => $row) {
            $courseTitle = isset($row['course_title']) ? trim($row['course_title']) : '';
            $matchedCourse = null;

            if (!empty($courseTitle)) {
                $courseTitleLower = strtolower($courseTitle);
                if (isset($coursesMap[$courseTitleLower])) {
                    $matchedCourse = $coursesMap[$courseTitleLower];
                } else {
                    // Try partial match
                    foreach ($coursesMap as $key => $course) {
                        if (strpos($key, $courseTitleLower) !== false || strpos($courseTitleLower, $key) !== false) {
                            $matchedCourse = $course;
                            break;
                        }
                    }
                }
            }

            $previewData[] = [
                'row_number' => $index + 2, // +2 because index starts at 0 and CSV has header
                'course_title' => $courseTitle,
                'matched_course' => $matchedCourse,
                'schedule_days' => $row['schedule_days'] ?? '',
                'schedule_time_start' => $row['schedule_time_start'] ?? '',
                'schedule_time_end' => $row['schedule_time_end'] ?? '',
                'schedule_location' => $row['schedule_location'] ?? '',
                'schedule_type' => $row['schedule_type'] ?? 'regular',
                'raw_data' => $row
            ];

            if (!$matchedCourse) {
                $errors[] = "Row " . ($index + 2) . ": Course '{$courseTitle}' not found";
            }
        }

        $data = [
            'previewData' => $previewData,
            'fileName' => $fileName,
            'errors' => $errors,
            'title' => 'Preview Schedule Upload'
        ];

        return view('preview_schedule_upload', $data);
    }

    /**
     * Import schedule data from uploaded file
     */
    public function importSchedule()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $parsedData = $session->get('schedule_upload_data');
        $fileName = $session->get('schedule_upload_file');

        if (empty($parsedData)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No schedule data found'
            ]);
        }

        $courseModel = new CourseModel();
        $allCourses = $courseModel->findAll();
        $coursesMap = [];
        foreach ($allCourses as $course) {
            $coursesMap[strtolower(trim($course['title']))] = $course;
        }

        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($parsedData as $index => $row) {
            $courseTitle = isset($row['course_title']) ? trim($row['course_title']) : '';
            if (empty($courseTitle)) {
                $failed++;
                $errors[] = "Row " . ($index + 2) . ": Course title is empty";
                continue;
            }

            $courseTitleLower = strtolower($courseTitle);
            $matchedCourse = null;

            if (isset($coursesMap[$courseTitleLower])) {
                $matchedCourse = $coursesMap[$courseTitleLower];
            } else {
                // Try partial match
                foreach ($coursesMap as $key => $course) {
                    if (strpos($key, $courseTitleLower) !== false || strpos($courseTitleLower, $key) !== false) {
                        $matchedCourse = $course;
                        break;
                    }
                }
            }

            if (!$matchedCourse) {
                $failed++;
                $errors[] = "Row " . ($index + 2) . ": Course '{$courseTitle}' not found";
                continue;
            }

            // Prepare update data
            $updateData = [
                'schedule_days' => $row['schedule_days'] ?? null,
                'schedule_time_start' => $row['schedule_time_start'] ?? null,
                'schedule_time_end' => $row['schedule_time_end'] ?? null,
                'schedule_location' => $row['schedule_location'] ?? null,
                'schedule_type' => $row['schedule_type'] ?? 'regular',
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // If teacher, check if they own this course
            if ($session->get('role') === 'teacher') {
                $userId = $session->get('user_id');
                if ($matchedCourse['instructor_id'] != $userId) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": You don't have permission to update schedule for '{$courseTitle}'";
                    continue;
                }
            }

            try {
                if ($courseModel->update($matchedCourse['id'], $updateData)) {
                    $imported++;
                } else {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": Failed to update schedule for '{$courseTitle}'";
                }
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 2) . ": Error - " . $e->getMessage();
            }
        }

        // Clean up uploaded file
        $uploadPath = WRITEPATH . 'uploads/schedules/';
        if ($fileName && file_exists($uploadPath . $fileName)) {
            @unlink($uploadPath . $fileName);
        }

        // Clear session data
        $session->remove('schedule_upload_data');
        $session->remove('schedule_upload_file');

        return $this->response->setJSON([
            'success' => true,
            'message' => "Schedule import completed. {$imported} courses updated, {$failed} failed.",
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors
        ]);
    }

    /**
     * Parse schedule file (CSV/Excel)
     */
    private function parseScheduleFile($filePath, $extension)
    {
        $data = [];

        if ($extension === 'csv' || $extension === 'txt') {
            // Parse CSV
            if (($handle = fopen($filePath, 'r')) !== false) {
                $header = fgetcsv($handle); // Read header row
                
                if (!$header) {
                    fclose($handle);
                    return [];
                }

                // Normalize header names
                $headerMap = [
                    'course' => 'course_title',
                    'course title' => 'course_title',
                    'course_name' => 'course_title',
                    'days' => 'schedule_days',
                    'schedule days' => 'schedule_days',
                    'day' => 'schedule_days',
                    'start time' => 'schedule_time_start',
                    'start_time' => 'schedule_time_start',
                    'time start' => 'schedule_time_start',
                    'end time' => 'schedule_time_end',
                    'end_time' => 'schedule_time_end',
                    'time end' => 'schedule_time_end',
                    'location' => 'schedule_location',
                    'room' => 'schedule_location',
                    'type' => 'schedule_type',
                    'schedule type' => 'schedule_type'
                ];

                $normalizedHeader = [];
                foreach ($header as $col) {
                    $colLower = strtolower(trim($col));
                    $normalizedHeader[] = $headerMap[$colLower] ?? $colLower;
                }

                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) !== count($normalizedHeader)) {
                        continue; // Skip malformed rows
                    }

                    $rowData = [];
                    foreach ($normalizedHeader as $index => $field) {
                        $rowData[$field] = isset($row[$index]) ? trim($row[$index]) : '';
                    }

                    // Only add rows with course title
                    if (!empty($rowData['course_title'])) {
                        $data[] = $rowData;
                    }
                }

                fclose($handle);
            }
        } else {
            // For Excel files, we'll use a simple CSV-like approach
            // Note: For full Excel support, you'd need to install PhpSpreadsheet
            return [];
        }

        return $data;
    }
}
