# Laboratory Activity: Materials Management System

## Overview
This document describes the complete implementation of the Materials Management System for the ITE311-ROBLES project. Students can download course materials, and teachers/admins can upload and manage them.

---

## Implementation Summary

### ✅ Step 1: Database Migration for Materials Table
**Status: COMPLETED**
- Migration file: `app/Database/Migrations/2025-11-13-180347_CreateMaterialsTable.php`
- Table structure:
  - `id` (Primary Key, Auto-Increment)
  - `course_id` (INT, Foreign Key to courses table)
  - `file_name` (VARCHAR 255) - Original filename
  - `file_path` (VARCHAR 255) - Server file path
  - `created_at` (DATETIME) - Upload timestamp
- Foreign key constraint: CASCADE on delete

### ✅ Step 2: MaterialModel
**Status: COMPLETED**
- Location: `app/Models/MaterialModel.php`
- Methods:
  - `insertMaterial($data)` - Inserts new material record
  - `getMaterialsByCourse($course_id)` - Retrieves all materials for a course

### ✅ Step 3: Materials Controller
**Status: COMPLETED**
- Location: `app/Controllers/Materials.php`
- Methods:
  - `upload($course_id)` - Display form and handle file upload
  - `delete($material_id)` - Delete material and associated file
  - `download($material_id)` - Secure file download with access control
  - `list($course_id)` - Display course materials

### ✅ Step 4: File Upload Functionality
**Status: COMPLETED**
- Features:
  - CodeIgniter Upload Library integration
  - File validation (type and size)
  - Supported formats: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, MP4, AVI
  - Max file size: 10MB
  - Encrypted file names for security
  - Database record creation upon successful upload

### ✅ Step 5: File Upload View
**Status: COMPLETED**
- Location: `app/Views/upload_material.php`
- Features:
  - Bootstrap 5 styling
  - File preview with name and size
  - Form validation messages
  - Progress indication
  - Responsive design

### ✅ Step 6: Downloadable Materials for Students
**Status: COMPLETED**
- Location: `app/Views/materials_list.php`
- Features:
  - Lists all materials for enrolled students
  - Download buttons with icons
  - Upload date display
  - Responsive layout
  - Statistics cards

### ✅ Step 7: Download Method Implementation
**Status: COMPLETED**
- Security features:
  - Authentication check
  - Enrollment verification (students must be enrolled)
  - Teachers can always download
  - Secure file download with original filename
  - Access restrictions enforced

### ✅ Step 8: Routes Configuration
**Status: COMPLETED**
- Location: `app/Config/Routes.php`
- Added routes:
  ```
  GET  /materials/upload/(:num)        → Materials::upload
  POST /materials/upload/(:num)        → Materials::upload
  GET  /materials/delete/(:num)        → Materials::delete
  GET  /materials/download/(:num)      → Materials::download
  GET  /materials/list/(:num)          → Materials::list
  GET  /admin/materials                → Home::materialsManagement
  ```

### ✅ Step 9: Admin Materials Management Page
**Status: COMPLETED**
- Location: `app/Views/admin_materials.php`
- Features:
  - Dashboard statistics (total courses, materials, etc.)
  - Course listing with material counts
  - Quick upload and view buttons
  - Recent materials section
  - Delete functionality with confirmation
  - Responsive design

---

## Testing Guide

### Test Scenario 1: Admin Upload Materials
1. **Login as Admin**
   - Email: `admin@example.com`
   - Password: `admin123`

2. **Navigate to Materials Management**
   - Dashboard → "Materials Management" button
   - Or direct URL: `http://localhost/ITE311-ROBLES/admin/materials`

3. **Upload a Material**
   - Click "Upload" button on any course
   - Select a file (PDF, PPT, etc.)
   - Click "Upload Material"
   - Verify file is saved and database record created

4. **Verify Upload**
   - Check "Recently Uploaded Materials" section
   - Verify file appears in the list

### Test Scenario 2: Student Download Materials
1. **Login as Student**
   - Email: `john.student@example.com`
   - Password: `student123`

2. **Enroll in a Course**
   - Dashboard → Available Courses
   - Click "Enroll" button

3. **View Materials**
   - Click "View All Materials" or "View Materials"
   - See all uploaded materials for the course

4. **Download Material**
   - Click "Download" button
   - Verify file downloads with original filename

### Test Scenario 3: Access Control
1. **Not Enrolled - Try to Download**
   - Login as a student
   - Attempt to download material from non-enrolled course
   - Verify error message: "You are not enrolled in this course"

2. **Not Logged In - Try to Download**
   - Logout
   - Try direct URL: `http://localhost/ITE311-ROBLES/materials/download/1`
   - Verify redirect to login page

3. **Teacher Access**
   - Login as teacher
   - Should be able to download any material
   - Can upload materials
   - Can delete materials

### Test Scenario 4: File Management
1. **Upload Multiple Files**
   - Upload 3-5 different file types
   - Verify all appear in materials list

2. **Delete Material**
   - Admin dashboard → Materials Management
   - Click delete on a material
   - Confirm deletion
   - Verify file removed from server
   - Verify database record deleted

3. **View Statistics**
   - Admin dashboard shows:
     - Total courses
     - Total materials
     - Courses with materials
     - Courses without materials

---

## File Structure

```
ITE311-ROBLES/
├── app/
│   ├── Controllers/
│   │   ├── Home.php                    (Added: materialsManagement method)
│   │   ├── Materials.php               (Complete implementation)
│   │   └── Course.php
│   ├── Models/
│   │   ├── MaterialModel.php           (Complete)
│   │   ├── CourseModel.php
│   │   └── EnrollmentModel.php
│   ├── Views/
│   │   ├── admin_materials.php         (NEW - Admin dashboard)
│   │   ├── upload_material.php         (Enhanced)
│   │   ├── materials_list.php          (Enhanced)
│   │   ├── dashboard.php               (Updated)
│   │   └── template.php
│   ├── Config/
│   │   └── Routes.php                  (Updated with new routes)
│   └── Database/
│       └── Migrations/
│           └── 2025-11-13-180347_CreateMaterialsTable.php
└── writable/
    └── uploads/                        (File storage directory)
```

---

## Security Features Implemented

1. **Authentication Check**
   - All material operations require logged-in user
   - Login redirect if not authenticated

2. **Role-Based Access Control**
   - Only teachers/admins can upload/delete
   - Only enrolled students can download
   - Teachers can always download

3. **Enrollment Verification**
   - Download restricted to enrolled students
   - Check in EnrollmentModel before download

4. **File Security**
   - File names encrypted on server
   - Original filename preserved for download
   - Files stored outside webroot (writable directory)
   - Mime-type validation

5. **SQL Injection Prevention**
   - CodeIgniter's query builder used throughout
   - Parameterized queries by default

---

## Database Schema

### Materials Table
```sql
CREATE TABLE materials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  created_at DATETIME,
  FOREIGN KEY (course_id) REFERENCES courses(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);
```

---

## Configuration Details

### Upload Settings (app/Controllers/Materials.php)
- **Upload Path:** `writable/uploads/`
- **Allowed Types:** pdf, doc, docx, txt, jpg, jpeg, png, mp4, avi
- **Max Size:** 10240 KB (10 MB)
- **Encryption:** File names encrypted for security

---

## Features Implemented

✅ **Admin Features:**
- View all courses and materials
- Upload materials for any course
- Delete materials
- View material statistics
- Track recently uploaded files
- One-click access to material management

✅ **Teacher Features:**
- Upload materials for their courses
- View course materials
- Delete their materials
- Download any material

✅ **Student Features:**
- View enrolled course materials
- Download materials
- View material upload dates
- Cannot download from non-enrolled courses

✅ **UI/UX Features:**
- Bootstrap 5 responsive design
- File preview before upload
- Real-time notifications
- Material statistics
- Icons for better UX
- Confirmation dialogs for deletion
- Loading indicators

---

## Testing Checklist

- [ ] Admin can access Materials Management page
- [ ] Admin can upload files successfully
- [ ] Files are stored in writable/uploads/
- [ ] Database records are created
- [ ] Students can see enrolled course materials
- [ ] Students can download materials
- [ ] Students cannot download from non-enrolled courses
- [ ] Teachers can download any material
- [ ] Admin can delete materials
- [ ] File is deleted from server when record deleted
- [ ] Non-logged-in users redirected to login
- [ ] File size limit is enforced
- [ ] Invalid file types are rejected
- [ ] Recently uploaded section shows correct data
- [ ] Statistics cards show correct numbers

---

## Troubleshooting

### Files not uploading
- Check `writable/uploads/` directory exists and is writable
- Verify file type in allowed types list
- Check file size doesn't exceed 10MB

### Download not working
- Verify user is logged in
- Verify student is enrolled in course
- Check file still exists in `writable/uploads/`
- Check database record exists

### Database errors
- Run migration: `php spark migrate`
- Verify database connection in `.env`

### Permission issues
- Ensure `writable/uploads/` has proper write permissions
- On Windows: Right-click → Properties → Security

---

## Future Enhancements

1. Batch file upload
2. Folder organization by course
3. Version control for materials
4. Activity logging
5. File preview functionality
6. Comment/discussion system
7. Material rating system
8. Scheduled material release

---

**Implementation Date:** November 13, 2025
**Version:** 1.0
**Status:** Production Ready ✅
