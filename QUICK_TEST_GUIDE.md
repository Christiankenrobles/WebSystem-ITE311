# Quick Testing Guide - Materials Management System

## 🚀 Quick Start URLs

### Admin Materials Management
```
http://localhost/ITE311-ROBLES/admin/materials
```

### Upload Material (by Course ID)
```
http://localhost/ITE311-ROBLES/materials/upload/1
http://localhost/ITE311-ROBLES/materials/upload/2
```

### View Course Materials
```
http://localhost/ITE311-ROBLES/materials/list/1
http://localhost/ITE311-ROBLES/materials/list/2
```

---

## 📋 Test Users

### Admin Account
```
Email:    admin@example.com
Password: admin123
Role:     admin
```
✅ Can upload, delete, and manage all materials

### Teacher Account
```
Email:    alice.instructor@example.com
Password: instructor123
Role:     teacher
```
✅ Can upload and delete materials, download any file

### Student Account
```
Email:    john.student@example.com
Password: student123
Role:     student
```
✅ Can view and download only enrolled course materials

---

## 🧪 Test Scenarios

### Scenario 1: Admin Uploading Materials
```
1. Login as admin
2. Go to Dashboard
3. Click "Materials Management" button
4. Find a course
5. Click "Upload" button
6. Select a PDF/image file
7. Click "Upload Material"
8. Verify success message and file appears in "Recently Uploaded Materials"
```

### Scenario 2: Student Viewing and Downloading
```
1. Login as student (john.student@example.com)
2. Go to Dashboard
3. Enroll in a course (if not already enrolled)
4. Under "Enrolled Courses", click "View All Materials"
5. See list of materials
6. Click "Download" button
7. File should download with original name
```

### Scenario 3: Access Control Test
```
1. Login as student
2. Go to different enrolled course
3. Try to access materials from NON-ENROLLED course:
   http://localhost/ITE311-ROBLES/materials/list/2
4. Should see error: "You are not enrolled in this course"
```

### Scenario 4: Admin Dashboard Statistics
```
1. Login as admin
2. Go to Dashboard
3. Click "Materials Management"
4. Verify cards show:
   ✓ Total Courses
   ✓ Total Materials
   ✓ Courses with Materials
   ✓ Courses without Materials
5. View courses table with upload/view buttons
6. Check recent materials list
```

---

## 📁 File Upload Testing

### Supported File Types
- 📄 PDF
- 📝 DOC, DOCX
- 📋 TXT
- 🖼️ JPG, JPEG, PNG
- 🎬 MP4, AVI

### File Size Limit
- Maximum: 10 MB

### Test Files
```
✓ test.pdf (2 MB)
✓ presentation.pptx (rejected - format not allowed)
✓ document.docx (3 MB)
✓ image.jpg (1 MB)
✓ large_file.zip (15 MB - rejected - too large)
```

---

## ✅ Verification Checklist

### Admin Features
- [ ] Can access Materials Management page
- [ ] Can upload files to courses
- [ ] Can delete materials with confirmation
- [ ] Can view recently uploaded materials
- [ ] Statistics update correctly
- [ ] Can view upload status in Recent Materials

### Student Features
- [ ] Can see enrolled course materials
- [ ] Can download materials
- [ ] Gets error for non-enrolled courses
- [ ] Download filename matches original

### Teacher Features
- [ ] Can upload materials
- [ ] Can delete materials
- [ ] Can download any material
- [ ] Dashboard shows their courses

### Security
- [ ] Non-logged-in users redirected to login
- [ ] Students can't download non-enrolled materials
- [ ] File permissions checked correctly
- [ ] Database records properly saved

---

## 🔍 Debugging Tips

### Check File Upload
```php
// Files stored in:
writable/uploads/

// View uploaded files:
// Open in file explorer or terminal
cd writable/uploads
ls -la  (Linux/Mac)
dir    (Windows)
```

### Check Database
```sql
-- View all materials
SELECT * FROM materials;

-- View materials for specific course
SELECT * FROM materials WHERE course_id = 1;

-- View material download count
SELECT COUNT(*) as total_materials FROM materials;
```

### Check Logs
```
writable/logs/log-*.log
```

---

## 🆘 Common Issues & Solutions

### Issue: Files not uploading
**Solution:**
```bash
# Check writable directory permissions
chmod 755 writable/uploads/
chmod 755 writable/

# Or verify in Windows File Explorer:
# Right-click → Properties → Security → Full Control
```

### Issue: Download not working
**Solution:**
- Verify student is enrolled
- Check file exists in writable/uploads/
- Check database record exists

### Issue: Enrollment check failing
**Solution:**
- Verify student enrolled in course
- Check enrollments table has record
- Verify course_id matches

### Issue: File type rejected
**Solution:**
- Only these types allowed: pdf, doc, docx, txt, jpg, jpeg, png, mp4, avi
- Convert file to supported format

---

## 📊 Database Queries

### Get all materials
```sql
SELECT * FROM materials;
```

### Get materials by course
```sql
SELECT * FROM materials WHERE course_id = 1;
```

### Get material details
```sql
SELECT m.*, c.title as course_title 
FROM materials m 
JOIN courses c ON m.course_id = c.id;
```

### Check total materials uploaded
```sql
SELECT COUNT(*) as total_uploads FROM materials;
```

### Find recent materials (last 24 hours)
```sql
SELECT * FROM materials 
WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY created_at DESC;
```

---

## 🎯 Expected Behavior

✅ **Successful Upload:**
- Page shows "Successfully uploaded!" message
- File appears in Recently Uploaded Materials within 1 second
- Database record created
- File stored in writable/uploads/

✅ **Successful Download:**
- File downloads with original filename
- No page reload
- File opens with correct application

✅ **Access Denied:**
- Redirected or shown error message
- No file downloads
- User stays on current page

---

## 🔐 Security Verification

```
✅ Authentication:
   - Non-logged users → Login page

✅ Authorization:
   - Students → Can only access enrolled courses
   - Teachers → Can access their courses
   - Admins → Can access everything

✅ Data Validation:
   - File type checked
   - File size checked
   - Filename sanitized

✅ SQL Security:
   - Parameterized queries used
   - CodeIgniter ORM prevents injection
```

---

## 📞 Support

For issues or questions, check:
1. MATERIALS_SETUP_GUIDE.md (detailed documentation)
2. app/Controllers/Materials.php (code comments)
3. writable/logs/ (error logs)

**Version:** 1.0
**Last Updated:** November 13, 2025
