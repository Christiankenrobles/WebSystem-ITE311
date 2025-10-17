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

        if ($session->get('role') === 'student') {
            $courseModel = new \App\Models\CourseModel();
            $userId = $session->get('user_id');
            $data['enrolledCourses'] = $courseModel->getUserEnrollments($userId);
            $data['availableCourses'] = $courseModel->getAvailableCourses($userId);
        }

        return view('dashboard', $data);
    }
}