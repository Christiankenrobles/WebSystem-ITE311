<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Show login page
     */
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Process login form
     */
    public function processLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email not found');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid password');
        }

        // Set session
        session()->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'logged_in' => true
        ]);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['username']);
    }

    /**
     * Show register page
     */
    public function register()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    /**
     * Process register form
     */
    public function processRegister()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => 'student', // Default role for new users
            'is_active' => 1
        ];

        if ($this->userModel->insert($data)) {
            $user = $this->userModel->where('email', $data['email'])->first();
            
            // Auto-login after registration
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'logged_in' => true
            ]);

            return redirect()->to('/dashboard')->with('success', 'Registration successful! Welcome to the system.');
        }

        return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
    }

    /**
     * Logout user
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Logged out successfully');
    }

    /**
     * API: Check if user is authenticated
     */
    public function checkAuth()
    {
        $userId = session()->get('user_id');

        if ($userId) {
            $user = $this->userModel->find($userId);
            return $this->response->setJSON([
                'authenticated' => true,
                'user' => $user
            ])->setStatusCode(200);
        }

        return $this->response->setJSON([
            'authenticated' => false
        ])->setStatusCode(401);
    }
}
