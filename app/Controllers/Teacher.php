<?php

namespace App\Controllers;

class Teacher extends BaseController
{
    public function dashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return redirect()->to(base_url('login'));
        }

        return view('teacher_dashboard');
    }
}
