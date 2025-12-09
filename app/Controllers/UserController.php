<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function index()
    {
        $data['users'] = $this->model->findAll();
        return view('users/index', $data);
    }

    public function show($id = null)
    {
        $data['user'] = $this->model->find($id);
        return view('users/show', $data);
    }

    public function new()
    {
        return view('users/create');
    }

    public function create()
    {
        $data = $this->request->getPost();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        if ($this->model->insert($data)) {
            return redirect()->to('/users');
        }
        return redirect()->back()->withInput();
    }

    public function edit($id = null)
    {
        $data['user'] = $this->model->find($id);
        return view('users/edit', $data);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost();
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        if ($this->model->update($id, $data)) {
            return redirect()->to('/users');
        }
        return redirect()->back()->withInput();
    }

    public function delete($id = null)
    {
        $this->model->delete($id);
        return redirect()->to('/users');
    }
}
