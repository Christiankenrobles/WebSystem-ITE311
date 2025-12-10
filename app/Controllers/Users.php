<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied');
        }

        // Get all users with pagination
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $data = [
            'users' => $this->userModel->limit($perPage, $offset)->findAll(),
            'totalUsers' => $this->userModel->countAllResults(),
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($this->userModel->countAllResults() / $perPage),
            'title' => 'Manage Users'
        ];

        return view('manage_users', $data);
    }

    public function edit($id)
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('users')->with('error', 'User not found');
        }

        $data = [
            'user' => $user,
            'title' => 'Edit User: ' . $user['name']
        ];

        return view('edit_user', $data);
    }

    public function update($id)
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('users')->with('error', 'User not found');
        }

        // Get input
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');
        $password = $this->request->getPost('password');

        // Validate
        if (empty($name) || empty($email) || empty($role)) {
            return redirect()->back()->with('error', 'All fields are required');
        }

        $updateData = [
            'name' => $name,
            'email' => $email,
            'role' => $role
        ];

        // Update password if provided
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $updateData)) {
            return redirect()->to('users')->with('success', 'User updated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to update user');
        }
    }

    public function delete($id)
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Prevent deleting yourself
        if ($id == $session->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot delete your own account']);
        }

        if ($this->userModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
        }
    }

    public function updateRole()
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Get JSON data
        $data = $this->request->getJSON(true);
        $userId = $data['user_id'] ?? null;
        $newRole = $data['role'] ?? null;

        // Validate input
        if (!$userId || !$newRole) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid input']);

                // Check if user exists
                $user = $this->userModel->find($userId);
                if (!$user) {
                    return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
                }

                // Prevent changing admin role
                if ($user['role'] === 'admin') {
                    return $this->response->setJSON(['success' => false, 'message' => 'Admin role cannot be changed']);
                }
        }

        // Validate role
        $validRoles = ['admin', 'teacher', 'student'];
        if (!in_array($newRole, $validRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid role']);
        }

        // Check if user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // Update role
        if ($this->userModel->update($userId, ['role' => $newRole])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Role updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update role']);
        }
    }

    /**
     * Create a new user (admin only)
     */
    public function create()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $data = $this->request->getJSON(true);
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = $data['role'] ?? 'student';
        $password = $data['password'] ?? '';

        if (!$name || !$email || !$password) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        // Validate role
        $validRoles = ['admin', 'teacher', 'student'];
        if (!in_array($role, $validRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid role']);
        }

        // Check for existing email
        $exists = $this->userModel->where('email', $email)->first();
        if ($exists) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email already exists']);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $insertData = [
            'name' => $name,
            'email' => $email,
            'password' => $hashed,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $newId = $this->userModel->insert($insertData);
            if ($newId) {
                $newUser = $this->userModel->find($newId);
                return $this->response->setJSON(['success' => true, 'message' => 'User created', 'user' => $newUser]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create user']);
    }

    public function search()
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $searchTerm = $this->request->getVar('q') ?? '';
        $searchTerm = trim($searchTerm);

        if (empty($searchTerm)) {
            return $this->response->setJSON([
                'success' => true,
                'results' => [],
                'message' => 'No search term provided'
            ]);
        }

        try {
            $results = $this->userModel
                ->where('name LIKE', "%$searchTerm%")
                ->orWhere('email LIKE', "%$searchTerm%")
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'results' => $results,
                'total' => count($results)
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error searching users: ' . $e->getMessage()
            ]);
        }
    }
}
