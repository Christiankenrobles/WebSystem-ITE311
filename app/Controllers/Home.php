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
            $data['courses'] = $courseModel->where('created_by', $userId)->findAll();
            
            // Fetch materials for those courses
            $materials = [];
            foreach ($data['courses'] as $course) {
                $materials[$course['id']] = $materialModel->getMaterialsByCourse($course['id']);
            }
            $data['materials'] = $materials;
        } elseif ($userRole === 'admin') {
            // Fetch all courses and users for admin dashboard
            $userModel = new \App\Models\UserModel();
            $data['allCourses'] = $courseModel->findAll();
            $data['totalUsers'] = $userModel->countAllResults();
            $data['studentCount'] = $userModel->where('role', 'student')->countAllResults();
            $data['teacherCount'] = $userModel->where('role', 'teacher')->countAllResults();
            $data['totalCourses'] = count($data['allCourses']);
            
            // Recent activity
            $data['recentUsers'] = $userModel->orderBy('id', 'DESC')->limit(5)->findAll();
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

        // Get all courses
        $allCourses = $courseModel->findAll();
        $allMaterials = $materialModel->findAll();
        
        // Get materials grouped by course
        $materialsByCourseByCourse = [];
        $coursesWithMaterials = [];
        
        foreach ($allCourses as $course) {
            $materials = $materialModel->getMaterialsByCourse($course['id']);
            if (!empty($materials)) {
                $materialsByCourseByCourse[$course['id']] = $materials;
                $coursesWithMaterials[] = $course['id'];
            }
        }

        // Get recent materials
        $recentMaterials = $materialModel->orderBy('created_at', 'DESC')->limit(10)->findAll();

        $data = [
            'allCourses' => $allCourses,
            'allMaterials' => $allMaterials,
            'materialsByCourseByCourse' => $materialsByCourseByCourse,
            'coursesWithMaterials' => $coursesWithMaterials,
            'recentMaterials' => $recentMaterials,
        ];

        return view('admin_materials', $data);
    }
}