<?php /**
 * Course Creation Form Component
 * Fields: Course Name, Course Code, Description, Academic Year, Semester, Term, Schedule, Assigned Teacher, Status
 * Pre-filled with sample data as requested.
 */ ?>

<div class="course-form-card card shadow-sm border-0">
    <div class="card-body">
        <h4 class="card-title mb-3"><i class="fas fa-plus-circle me-2"></i>Create Course</h4>

        <form id="courseForm" onsubmit="event.preventDefault(); saveCourse();">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="course_name" class="form-label">Course Name</label>
                    <input id="course_name" name="course_name" class="form-control" required
                           pattern="[A-Za-z0-9 _-]+"
                           title="Special characters are not allowed. Only letters, numbers, spaces, hyphen (-) and underscore (_) are allowed."
                           value="Introduction to Information Technology">
                </div>

                <div class="col-12 col-md-6">
                    <label for="course_code" class="form-label">Course Code</label>
                    <input id="course_code" name="course_code" class="form-control" required
                           pattern="[A-Za-z0-9-]+"
                           title="Special characters are not allowed. Only letters, numbers, and hyphen (-) are allowed."
                           value="IT-101">
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required>Overview of IT concepts, hardware, software, and digital literacy.</textarea>
                </div>

                <div class="col-12 col-md-4">
                    <label for="academic_year" class="form-label">Academic Year</label>
                    <input id="academic_year" name="academic_year" class="form-control" required
                           value="2025–2026">
                </div>

                <div class="col-12 col-md-4">
                    <label for="semester" class="form-label">Semester</label>
                    <select id="semester" name="semester" class="form-select" required>
                        <option value="1st Semester" selected>1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="term" class="form-label">Term</label>
                    <select id="term" name="term" class="form-select" required>
                        <option value="Prelim–Midterm–Final" selected>Prelim–Midterm–Final</option>
                        <option value="Trimester">Trimester</option>
                        <option value="Quarter">Quarter</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="schedule" class="form-label">Schedule (date & time)</label>
                    <input id="schedule" name="schedule" class="form-control" required
                           value="Tue & Thu — 1:00 PM–3:00 PM">
                </div>

                <div class="col-12 col-md-4">
                    <label for="assigned_teacher" class="form-label">Assigned Teacher</label>
                    <select id="assigned_teacher" name="assigned_teacher" class="form-select" required>
                        <option value="alice.instructor@example.com" selected>Alice Instructor</option>
                        <option value="bob.instructor@example.com">Bob Instructor</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="Open" selected>Open</option>
                        <option value="Closed">Closed</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>

                <div class="col-12 text-end mt-2">
                    <button type="submit" class="btn btn-primary" id="saveCourseBtn">
                        <i class="fas fa-save me-2"></i>Save Course
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .course-form-card { max-width: 980px; margin: 0 auto 1.5rem; border-radius: 12px; }
    .course-form-card .card-body { padding: 1.25rem 1.5rem; }
    .course-form-card .card-title { font-weight: 700; color: #2c3e50; }

    /* Responsive helpers */
    @media (max-width: 576px) {
        .course-form-card { padding: 0.5rem; }
        .course-form-card .card-body { padding: 0.9rem; }
    }
</style>

<script>
    function showNotification(message, type = 'success') {
        const n = document.createElement('div');
        n.className = `toast-notification ${type}`;
        n.style.position = 'fixed';
        n.style.right = '1rem';
        n.style.bottom = '1rem';
        n.style.padding = '0.9rem 1rem';
        n.style.background = type === 'success' ? '#e6ffef' : '#fff0f0';
        n.style.borderLeft = type === 'success' ? '4px solid #27ae60' : '4px solid #e74c3c';
        n.style.boxShadow = '0 6px 18px rgba(0,0,0,0.08)';
        n.innerText = message;
        document.body.appendChild(n);
        setTimeout(() => { n.style.opacity = '0'; setTimeout(()=>n.remove(),300); }, 3000);
    }

    async function saveCourse() {
        const btn = document.getElementById('saveCourseBtn');
        btn.disabled = true;

        const payload = {
            course_name: document.getElementById('course_name').value.trim(),
            course_code: document.getElementById('course_code').value.trim(),
            description: document.getElementById('description').value.trim(),
            academic_year: document.getElementById('academic_year').value.trim(),
            semester: document.getElementById('semester').value,
            term: document.getElementById('term').value,
            schedule: document.getElementById('schedule').value.trim(),
            assigned_teacher: document.getElementById('assigned_teacher').value,
            status: document.getElementById('status').value
        };

        // Basic validation
        if (!payload.course_name || !payload.course_code) {
            showNotification('Please provide course name and code.', 'error');
            btn.disabled = false;
            return;
        }

        try {
            const form = document.getElementById('courseForm');
            const formData = new FormData(form);

            // Append payload fields (keeps CSRF field already in the form)
            Object.entries(payload).forEach(([k, v]) => formData.set(k, v));

            const res = await fetch('<?= site_url('course/create') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                data = { success: false, message: text || ('HTTP ' + res.status) };
            }

            if (res.ok && data && data.success) {
                showNotification('Course saved successfully!', 'success');

                // Reset the form (clears layout)
                form.reset();

                // Close modal if the form is inside the Create Course modal
                const modalEl = document.getElementById('createCourseModal');
                if (modalEl && window.bootstrap && bootstrap.Modal) {
                    const instance = bootstrap.Modal.getInstance(modalEl);
                    if (instance) {
                        instance.hide();
                    }
                }
            } else {
                showNotification((data && data.message ? data.message : 'Failed to save course') + ' (HTTP ' + res.status + ')', 'error');
            }
        } catch (err) {
            console.error(err);
            showNotification('Error saving course: ' + (err?.message || 'Unknown error'), 'error');
        } finally {
            btn.disabled = false;
        }
    }
</script>
