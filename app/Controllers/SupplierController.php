<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use CodeIgniter\RESTful\ResourceController;

class SupplierController extends ResourceController
{
    protected $modelName = 'App\Models\SupplierModel';
    protected $format    = 'json';

    public function index()
    {
        $data['suppliers'] = $this->model->findAll();
        return view('suppliers/index', $data);
    }

    public function show($id = null)
    {
        $data['supplier'] = $this->model->find($id);
        return view('suppliers/show', $data);
    }

    public function new()
    {
        return view('suppliers/create');
    }

    public function create()
    {
        $data = $this->request->getPost();
        if ($this->model->insert($data)) {
            return redirect()->to('/suppliers');
        }
        return redirect()->back()->withInput();
    }

    public function edit($id = null)
    {
        $data['supplier'] = $this->model->find($id);
        return view('suppliers/edit', $data);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost();
        if ($this->model->update($id, $data)) {
            return redirect()->to('/suppliers');
        }
        return redirect()->back()->withInput();
    }

    public function delete($id = null)
    {
        $this->model->delete($id);
        return redirect()->to('/suppliers');
    }
}
