<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    /**
     * Upload material for a course
     */
    public function upload($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first');
        }

        $userRole = $session->get('role');
        // Only admin and teachers can upload materials
        if (!in_array($userRole, ['admin', 'teacher'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You do not have permission to upload materials');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($course_id);

        if (!$course) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found');
        }

        // If teacher, check if they own this course
        if ($userRole === 'teacher') {
            $userId = $session->get('user_id');
            if ($course['instructor_id'] != $userId) {
                return redirect()->to(base_url('dashboard'))->with('error', 'You can only upload materials for your own courses');
            }
        }

        // Handle file upload
        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('material_file');
            
            if (!$file) {
                return redirect()->back()->withInput()->with('error', 'No file selected. Please choose a file to upload.');
            }
            
            if (!$file->isValid()) {
                $errorCode = $file->getError();
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File size exceeds server limit.',
                    UPLOAD_ERR_FORM_SIZE => 'File size exceeds form limit.',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
                ];
                $errorMsg = $errorMessages[$errorCode] ?? 'Unknown upload error (Code: ' . $errorCode . ')';
                return redirect()->back()->withInput()->with('error', 'Upload failed: ' . $errorMsg);
            }

            // Configure upload path
            $uploadPath = WRITEPATH . 'uploads/';
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    return redirect()->back()->withInput()->with('error', 'Failed to create upload directory. Please check folder permissions.');
                }
            }
            
            // Check if directory is writable
            if (!is_writable($uploadPath)) {
                return redirect()->back()->withInput()->with('error', 'Upload directory is not writable. Please check folder permissions.');
            }

            // STRICT VALIDATION - Only PDF and PPT/PPTX allowed
            $allowedExtensions = ['pdf', 'ppt', 'pptx'];
            $fileExtension = strtolower($file->getClientExtension());
            $mimeType = strtolower($file->getClientMimeType());
            
            // Check file extension
            if (!in_array($fileExtension, $allowedExtensions)) {
                return redirect()->back()->withInput()->with('error', 'Invalid file type. Only PDF and PPT/PPTX files are allowed. Your file: .' . $fileExtension);
            }

            // Additional MIME type validation for security
            $allowedMimeTypes = [
                'application/pdf',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ];
            
            if (!in_array($mimeType, $allowedMimeTypes)) {
                return redirect()->back()->withInput()->with('error', 'Invalid file type detected. Only PDF and PPT/PPTX files are allowed.');
            }

            // Validate file size (10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'File size exceeds 10MB limit');
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            
            // Move file
            try {
                if ($file->move($uploadPath, $newName)) {
                    $materialModel = new MaterialModel();
                    
                    // Ensure course_id is integer
                    $course_id = (int) $course_id;
                    
                    $data = [
                        'course_id' => $course_id,
                        'file_name' => $file->getClientName(),
                        'file_path' => 'uploads/' . $newName,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    try {
                        $insertResult = $materialModel->insert($data);
                        if ($insertResult) {
                            // Verify the insert was successful by checking the database
                            $insertedId = $materialModel->getInsertID();
                            
                            // Double-check: verify material was saved with correct course_id
                            $verifyMaterial = $materialModel->find($insertedId);
                            if (!$verifyMaterial || $verifyMaterial['course_id'] != $course_id) {
                                // Delete file if course_id mismatch
                                $fullPath = $uploadPath . $newName;
                                if (file_exists($fullPath)) {
                                    @unlink($fullPath);
                                }
                                return redirect()->back()->withInput()->with('error', 'Error: Material saved with incorrect course ID. Please try again.');
                            }
                            
                            // Check if request is AJAX
                            $isAjax = $this->request->isAJAX() || 
                                     $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' ||
                                     strpos($this->request->getHeaderLine('Accept'), 'application/json') !== false;
                            
                            if ($isAjax) {
                                // Return JSON response for AJAX requests
                                $courseModel = new CourseModel();
                                $course = $courseModel->find($course_id);
                                $courseName = $course ? $course['title'] : 'Course ID: ' . $course_id;
                                
                                // Set proper JSON response headers
                                return $this->response
                                    ->setContentType('application/json')
                                    ->setJSON([
                                        'success' => true,
                                        'message' => 'Material uploaded successfully!',
                                        'material' => [
                                            'id' => $insertedId,
                                            'file_name' => $file->getClientName(),
                                            'course_id' => (int)$course_id,
                                            'course_name' => $courseName,
                                            'created_at' => date('Y-m-d H:i:s')
                                        ]
                                    ]);
                            }
                            
                            // Regular redirect for non-AJAX requests
                            $successMessage = 'Material uploaded successfully: ' . $file->getClientName();
                            return redirect()->to(base_url('admin/materials'))->with('success', $successMessage);
                        } else {
                            // Delete uploaded file if database insert fails
                            $fullPath = $uploadPath . $newName;
                            if (file_exists($fullPath)) {
                                @unlink($fullPath);
                            }
                            return redirect()->back()->withInput()->with('error', 'Failed to save material record to database. Please try again.');
                        }
                    } catch (\Exception $e) {
                        // Delete uploaded file if error occurs
                        $fullPath = $uploadPath . $newName;
                        if (file_exists($fullPath)) {
                            @unlink($fullPath);
                        }
                        return redirect()->back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
                    }
                } else {
                    $errors = $file->getErrorString();
                    return redirect()->back()->withInput()->with('error', 'Failed to move uploaded file: ' . $errors);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Upload error: ' . $e->getMessage());
            }
        }

        // Display upload form
        $data = [
            'course_id' => $course_id,
            'course' => $course,
            'title' => 'Upload Material - ' . $course['title']
        ];

        return view('upload_material', $data);
    }

    /**
     * List materials for a course
     */
    public function list($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first');
        }

        $courseModel = new CourseModel();
        $materialModel = new MaterialModel();

        $course = $courseModel->find($course_id);
        if (!$course) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found');
        }

        // Fetch fresh materials from database (ensures latest data after upload)
        $materials = $materialModel->where('course_id', $course_id)
                                   ->orderBy('created_at', 'DESC')
                                   ->findAll();
        
        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        // Check if user has access (enrolled student, teacher of course, or admin)
        $hasAccess = false;
        if ($userRole === 'admin') {
            $hasAccess = true;
        } elseif ($userRole === 'teacher' && $course['instructor_id'] == $userId) {
            $hasAccess = true;
        } elseif ($userRole === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $enrollment = $enrollmentModel->where('user_id', $userId)
                                         ->where('course_id', $course_id)
                                         ->first();
            $hasAccess = $enrollment !== null;
        }

        if (!$hasAccess) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You do not have access to view materials for this course');
        }

        $data = [
            'course' => $course,
            'materials' => $materials,
            'userRole' => $userRole,
            'canUpload' => in_array($userRole, ['admin', 'teacher']) && ($userRole === 'admin' || $course['instructor_id'] == $userId),
            'title' => 'Course Materials - ' . $course['title']
        ];

        return view('materials_list', $data);
    }

    /**
     * Download a material file
     */
    public function download($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($material['course_id']);

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        // Check access
        $hasAccess = false;
        if ($userRole === 'admin') {
            $hasAccess = true;
        } elseif ($userRole === 'teacher' && $course['instructor_id'] == $userId) {
            $hasAccess = true;
        } elseif ($userRole === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $enrollment = $enrollmentModel->where('user_id', $userId)
                                         ->where('course_id', $material['course_id'])
                                         ->first();
            $hasAccess = $enrollment !== null;
        }

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have permission to download this material');
        }

        $filePath = WRITEPATH . $material['file_path'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server');
        }

        return $this->response->download($filePath, null)->setFileName($material['file_name']);
    }

    /**
     * Delete a material
     */
    public function delete($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first');
        }

        $userRole = $session->get('role');
        // Only admin and teachers can delete materials
        if (!in_array($userRole, ['admin', 'teacher'])) {
            return redirect()->back()->with('error', 'You do not have permission to delete materials');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($material['course_id']);

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        // If teacher, check if they own this course
        if ($userRole === 'teacher') {
            $userId = $session->get('user_id');
            if ($course['instructor_id'] != $userId) {
                return redirect()->back()->with('error', 'You can only delete materials from your own courses');
            }
        }

        // Delete file from server
        $filePath = WRITEPATH . $material['file_path'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // Delete from database
        $courseId = $material['course_id'];
        if ($materialModel->delete($material_id)) {
            return redirect()->to(base_url('materials/list/' . $courseId))->with('success', 'Material deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material');
        }
    }
}

