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

        // Get all users with pagination, ordered by ID ascending
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Get total count first
        $totalUsers = $this->userModel->countAllResults();
        
        // Get paginated users, ordered by ID ascending
        $users = $this->userModel->orderBy('id', 'ASC')
                               ->findAll($perPage, $offset);

        $data = [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'currentPage' => (int)$page,
            'perPage' => $perPage,
            'totalPages' => ceil($totalUsers / $perPage),
            'title' => 'Manage Users',
            'pager' => $this->userModel->pager
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

    public function delete($id = null)
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Get user ID from parameter or JSON body
        if ($id === null) {
            $data = $this->request->getJSON(true);
            $id = $data['user_id'] ?? null;
        }

        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID is required']);
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


    public function add()
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Add New User',
            'validation' => \Config\Services::validation()
        ];

        return view('add_user', $data);
    }

    public function create()
    {
        // Check if user is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied');
        }

        // Validate input
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[A-Za-z0-9 _\-]+$/]',
            'username' => 'required|min_length[3]|max_length[50]|regex_match[/^[A-Za-z0-9_\-]+$/]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'matches[password]',
            'role' => 'required|in_list[admin,teacher,student]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare user data
        $data = [
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Save to database
        try {
            $this->userModel->insert($data);
            return redirect()->to('/users')->with('success', 'User added successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error adding user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to add user: ' . $e->getMessage());
        }
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
