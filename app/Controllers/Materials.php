Step 5: Create the File Upload View
Create a view file.
The view should contain a form with the enctype="multipart/form-data" attribute and a file input field.
Style the form using Bootstrap classes.
<?php

namespace App\Controllers;

use CodeIgniter\Files\File;

class Materials extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function upload($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return redirect()->to(base_url('login'));
        }

        if ($this->request->getMethod() === 'post') {
            // Load File Uploading Library
            $upload = \Config\Services::upload();
            $validation = \Config\Services::validation();

            // Configure upload preferences
            $upload->initialize([
                'upload_path' => WRITEPATH . 'uploads/',
                'allowed_types' => 'pdf|doc|docx|txt|jpg|jpeg|png|mp4|avi',
                'max_size' => 10240, // 10MB
                'encrypt_name' => true,
            ]);

            if (!$upload->do_upload('material_file')) {
                return redirect()->to(base_url('materials/upload/' . $course_id))->with('error', $upload->display_errors());
            } else {
                $fileData = $upload->data();
                $materialModel = new \App\Models\MaterialModel();
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $fileData['client_name'],
                    'file_path' => $fileData['file_name'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                if ($materialModel->insert($data)) {
                    return redirect()->to(base_url('teacher/dashboard'))->with('success', 'Material uploaded successfully');
                } else {
                    return redirect()->to(base_url('materials/upload/' . $course_id))->with('error', 'Failed to save material to database');
                }
            }
        }

        return view('upload_material', ['course_id' => $course_id]);
    }

    public function delete($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return redirect()->to(base_url('login'));
        }

        $materialModel = new \App\Models\MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material) {
            // Delete the file from the server
            $filePath = WRITEPATH . $material['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the record from the database
            if ($materialModel->delete($material_id)) {
                return redirect()->back()->with('success', 'Material deleted successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to delete material');
            }
        } else {
            return redirect()->back()->with('error', 'Material not found');
        }
    }

    public function download($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $materialModel = new \App\Models\MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material) {
            // Check if user is enrolled in the course
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $enrollment = $enrollmentModel->where('user_id', $session->get('user_id'))
                                          ->where('course_id', $material['course_id'])
                                          ->first();

            if ($enrollment || $session->get('role') === 'teacher') {
                $filePath = WRITEPATH . $material['file_path'];
                if (file_exists($filePath)) {
                    return $this->response->download($filePath, null, true)->setFileName($material['file_name']);
                } else {
                    return redirect()->back()->with('error', 'File not found on server');
                }
            } else {
                return redirect()->back()->with('error', 'You are not enrolled in this course');
            }
        } else {
            return redirect()->back()->with('error', 'Material not found');
        }
    }

    public function list($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // Check if user is enrolled in the course or is a teacher
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->where('user_id', $session->get('user_id'))
                                      ->where('course_id', $course_id)
                                      ->first();

        if (!$enrollment && $session->get('role') !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not enrolled in this course');
        }

        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->find($course_id);

        if (!$course) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found');
        }

        $materialModel = new \App\Models\MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($course_id);

        return view('materials_list', ['course' => $course, 'materials' => $materials]);
    }
}
