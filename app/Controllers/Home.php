<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('index'); // Homepage
    }

    public function about()
    {
        return view('about'); // About page
    }

    public function contact() // Contact page
    {
        return view('contact');
    }

    public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [];
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        $courseModel = new \App\Models\CourseModel();
        $materialModel = new \App\Models\MaterialModel();

        if ($userRole === 'student') {
            $data['enrolledCourses'] = $courseModel->getUserEnrollments($userId);
            $data['availableCourses'] = $courseModel->getAvailableCourses($userId);

            // Fetch materials for enrolled courses
            $materials = [];
            foreach ($data['enrolledCourses'] as $course) {
                $materials[$course['id']] = $materialModel->getMaterialsByCourse($course['id']);
            }
            $data['materials'] = $materials;
        } elseif ($userRole === 'teacher') {
            // Fetch courses created by the teacher
            $data['courses'] = $courseModel->getCoursesByInstructor($userId);
            
            // Fetch materials for those courses
            $materials = [];
            foreach ($data['courses'] as $course) {
                $materials[$course['id']] = $materialModel->getMaterialsByCourse($course['id']);
            }
            $data['materials'] = $materials;
        } elseif ($userRole === 'admin') {
            // Fetch all courses and users for admin dashboard
            $userModel = new \App\Models\UserModel();
            $enrollmentModel = new \App\Models\EnrollmentModel();
            
            $data['allCourses'] = $courseModel->findAll();
            $data['totalUsers'] = $userModel->countAllResults();
            $data['studentCount'] = $userModel->where('role', 'student')->countAllResults();
            $data['teacherCount'] = $userModel->where('role', 'teacher')->countAllResults();
            $data['totalCourses'] = count($data['allCourses']);
            
            // Enrollment statistics
            $data['totalEnrollments'] = $enrollmentModel->countAllResults();
            $data['uniqueStudentsEnrolled'] = $enrollmentModel->distinct()->select('user_id')->countAllResults();
            $data['coursesWithEnrollments'] = $enrollmentModel->distinct()->select('course_id')->countAllResults();
            
            // Recent activity
            $data['recentUsers'] = $userModel->orderBy('id', 'DESC')->limit(5)->findAll();
            $data['recentEnrollments'] = $enrollmentModel->orderBy('enrollment_date', 'DESC')
                ->limit(5)
                ->findAll();
        }

        return view('dashboard', $data);
    }

    public function materialsManagement()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $courseModel = new \App\Models\CourseModel();
        $materialModel = new \App\Models\MaterialModel();

        // Get all courses (fresh from database)
        $allCourses = $courseModel->findAll();
        
        // Get all materials (fresh from database, no caching)
        $allMaterials = $materialModel->orderBy('created_at', 'DESC')->findAll();
        
        // Get materials grouped by course (fresh data)
        $materialsByCourseByCourse = [];
        $coursesWithMaterials = [];
        
        foreach ($allCourses as $course) {
            // Always fetch fresh materials for each course (even if empty)
            // Use direct query to ensure fresh data
            $materials = $materialModel->where('course_id', $course['id'])
                                      ->orderBy('created_at', 'DESC')
                                      ->findAll();
            // Always store materials array (even if empty) for accurate counting
            $materialsByCourseByCourse[$course['id']] = $materials;
            
            if (!empty($materials)) {
                $coursesWithMaterials[] = $course['id'];
            }
        }

        // Get recent materials (fresh from database) - ensure we get the latest
        $recentMaterials = $materialModel->orderBy('created_at', 'DESC')
                                        ->orderBy('id', 'DESC')
                                        ->limit(10)
                                        ->findAll();
        
        // Debug: Log recent materials count
        // log_message('debug', 'Recent materials count: ' . count($recentMaterials));

        $data = [
            'allCourses' => $allCourses,
            'allMaterials' => $allMaterials,
            'materialsByCourseByCourse' => $materialsByCourseByCourse,
            'coursesWithMaterials' => $coursesWithMaterials,
            'recentMaterials' => $recentMaterials,
        ];

        return view('admin_materials', $data);
    }

    /**
     * Admin view of all enrollments
     */
    public function adminEnrollments()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $courseModel = new \App\Models\CourseModel();
        $userModel = new \App\Models\UserModel();

        // Get all enrollments with course and user details
        $enrollments = $enrollmentModel->db->table('enrollments')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->join('users', 'enrollments.user_id = users.id')
            ->select('enrollments.*, courses.title as course_title, courses.description as course_description, 
                     courses.schedule_days, courses.schedule_time_start, courses.schedule_time_end, 
                     courses.schedule_location, courses.schedule_type,
                     users.name as student_name, users.email as student_email, users.role as student_role')
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->get()
            ->getResultArray();

        // Get statistics
        $totalEnrollments = count($enrollments);
        $uniqueStudents = count(array_unique(array_column($enrollments, 'user_id')));
        $uniqueCourses = count(array_unique(array_column($enrollments, 'course_id')));

        // Group by course
        $enrollmentsByCourse = [];
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment['course_id'];
            if (!isset($enrollmentsByCourse[$courseId])) {
                $enrollmentsByCourse[$courseId] = [
                    'course' => [
                        'id' => $courseId,
                        'title' => $enrollment['course_title'],
                        'description' => $enrollment['course_description'],
                        'schedule_days' => $enrollment['schedule_days'],
                        'schedule_time_start' => $enrollment['schedule_time_start'],
                        'schedule_time_end' => $enrollment['schedule_time_end'],
                        'schedule_location' => $enrollment['schedule_location'],
                        'schedule_type' => $enrollment['schedule_type'],
                    ],
                    'enrollments' => []
                ];
            }
            $enrollmentsByCourse[$courseId]['enrollments'][] = $enrollment;
        }

        $data = [
            'enrollments' => $enrollments,
            'enrollmentsByCourse' => $enrollmentsByCourse,
            'totalEnrollments' => $totalEnrollments,
            'uniqueStudents' => $uniqueStudents,
            'uniqueCourses' => $uniqueCourses,
            'title' => 'All Enrollments'
        ];

        return view('admin_enrollments', $data);
    }

    /**
     * Admin view of all schedules
     */
    public function adminSchedule()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $courseModel = new \App\Models\CourseModel();

        // Get all enrollments with schedule information
        $enrollments = $enrollmentModel->db->table('enrollments')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->join('users as students', 'enrollments.user_id = students.id')
            ->join('users as instructors', 'courses.instructor_id = instructors.id')
            ->select('enrollments.*, courses.*, 
                     students.name as student_name, students.email as student_email,
                     instructors.name as instructor_name, instructors.email as instructor_email')
            ->orderBy('courses.schedule_days', 'ASC')
            ->orderBy('courses.schedule_time_start', 'ASC')
            ->get()
            ->getResultArray();

        // Group by day
        $scheduleByDay = [];
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($enrollments as $enrollment) {
            $scheduleDays = $enrollment['schedule_days'] ?? '';
            if (!empty($scheduleDays)) {
                $days = explode(',', $scheduleDays);
                foreach ($days as $day) {
                    $day = trim($day);
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
                usort($scheduleByDay[$day], function($a, $b) {
                    $timeA = $a['schedule_time_start'] ?? '00:00:00';
                    $timeB = $b['schedule_time_start'] ?? '00:00:00';
                    return strcmp($timeA, $timeB);
                });
                $sortedSchedule[$day] = $scheduleByDay[$day];
            }
        }
        if (isset($scheduleByDay['Unscheduled'])) {
            $sortedSchedule['Unscheduled'] = $scheduleByDay['Unscheduled'];
        }

        // Get course statistics
        $coursesWithSchedule = count(array_filter($enrollments, function($e) { 
            return !empty($e['schedule_days']); 
        }));
        $totalEnrollments = count($enrollments);

        $data = [
            'enrollments' => $enrollments,
            'scheduleByDay' => $sortedSchedule,
            'daysOfWeek' => $daysOfWeek,
            'coursesWithSchedule' => $coursesWithSchedule,
            'totalEnrollments' => $totalEnrollments,
            'title' => 'All Schedules'
        ];

        return view('admin_schedule', $data);
    }

    /**
     * Enrollment Dashboard - Comprehensive view with statistics
     */
    public function enrollmentDashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Get all enrollments with details
        $enrollments = $enrollmentModel->db->table('enrollments')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->join('users as students', 'enrollments.user_id = students.id')
            ->join('users as instructors', 'courses.instructor_id = instructors.id')
            ->select('enrollments.*, courses.*, 
                     students.name as student_name, students.email as student_email, students.role as student_role,
                     instructors.name as instructor_name, instructors.email as instructor_email')
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate statistics
        $totalEnrollments = count($enrollments);
        $uniqueStudents = count(array_unique(array_column($enrollments, 'user_id')));
        $uniqueCourses = count(array_unique(array_column($enrollments, 'course_id')));

        // Enrollments by course
        $courseEnrollmentCounts = [];
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment['course_id'];
            if (!isset($courseEnrollmentCounts[$courseId])) {
                $courseEnrollmentCounts[$courseId] = [
                    'course_title' => $enrollment['title'],
                    'count' => 0
                ];
            }
            $courseEnrollmentCounts[$courseId]['count']++;
        }
        uasort($courseEnrollmentCounts, function ($a, $b) {
            return ($b['count'] ?? 0) <=> ($a['count'] ?? 0);
        });
        $topCourses = array_slice($courseEnrollmentCounts, 0, 5, true);

        // Enrollments by day (last 7 days)
        $enrollmentsByDay = [];
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $last7Days[] = $date;
            $enrollmentsByDay[$date] = 0;
        }
        foreach ($enrollments as $enrollment) {
            $enrollmentDate = date('Y-m-d', strtotime($enrollment['enrollment_date']));
            if (isset($enrollmentsByDay[$enrollmentDate])) {
                $enrollmentsByDay[$enrollmentDate]++;
            }
        }

        // Recent enrollments (last 10)
        $recentEnrollments = array_slice($enrollments, 0, 10);

        // Students with most enrollments
        $studentEnrollmentCounts = [];
        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment['user_id'];
            if (!isset($studentEnrollmentCounts[$studentId])) {
                $studentEnrollmentCounts[$studentId] = [
                    'name' => $enrollment['student_name'],
                    'email' => $enrollment['student_email'],
                    'count' => 0
                ];
            }
            $studentEnrollmentCounts[$studentId]['count']++;
        }
        uasort($studentEnrollmentCounts, function ($a, $b) {
            return ($b['count'] ?? 0) <=> ($a['count'] ?? 0);
        });
        $topStudents = array_slice($studentEnrollmentCounts, 0, 5, true);

        // Group enrollments by course for detailed view
        $enrollmentsGroupedByCourse = [];
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment['course_id'];
            if (!isset($enrollmentsGroupedByCourse[$courseId])) {
                $enrollmentsGroupedByCourse[$courseId] = [
                    'course' => [
                        'id' => $courseId,
                        'title' => $enrollment['title'],
                        'description' => $enrollment['description'],
                        'instructor_name' => $enrollment['instructor_name'],
                        'schedule_days' => $enrollment['schedule_days'],
                        'schedule_time_start' => $enrollment['schedule_time_start'],
                        'schedule_time_end' => $enrollment['schedule_time_end'],
                        'schedule_location' => $enrollment['schedule_location'],
                    ],
                    'enrollments' => []
                ];
            }
            $enrollmentsGroupedByCourse[$courseId]['enrollments'][] = $enrollment;
        }

        $data = [
            'totalEnrollments' => $totalEnrollments,
            'uniqueStudents' => $uniqueStudents,
            'uniqueCourses' => $uniqueCourses,
            'topCourses' => $topCourses,
            'topStudents' => $topStudents,
            'enrollmentsByDay' => $enrollmentsByDay,
            'last7Days' => $last7Days,
            'recentEnrollments' => $recentEnrollments,
            'enrollmentsGroupedByCourse' => $enrollmentsGroupedByCourse,
            'allEnrollments' => $enrollments,
            'title' => 'Enrollment Dashboard'
        ];

        return view('enrollment_dashboard', $data);
    }
}