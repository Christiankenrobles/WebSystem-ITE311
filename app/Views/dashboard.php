<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger">Logout</a>
    </div>

    <div class="alert alert-success" role="alert">
        Welcome, <?= esc(session('userEmail')) ?>! You are logged in as a <?= esc(session('role')) ?>.
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (session('role') === 'admin'): ?>
                <p class="mb-0">Admin Dashboard: Manage users, courses, and system settings.</p>
            <?php elseif (session('role') === 'teacher'): ?>
                <p class="mb-0">Teacher Dashboard: Create and manage courses and lessons.</p>
            <?php else: ?>
                <p class="mb-0">Student Dashboard: Enroll in courses and take quizzes.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Interface Section -->
    <div class="card shadow-sm border-0 mt-4 bg-light">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-search text-primary"></i> Search Courses
            </h5>
            <form method="GET" action="<?= site_url('course/search') ?>" id="search-form" class="w-100">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control border-2" id="search-input" name="q" 
                           placeholder="Search courses by title or description..." 
                           autocomplete="off" aria-label="Search courses">
                    <button class="btn btn-primary" type="submit" id="search-btn">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle"></i> Tip: Search by course name (e.g., "PHP", "Web") or keywords
                </small>
            </form>
        </div>
    </div>

    <!-- Search Suggestions (AJAX Autocomplete) -->
    <div id="search-suggestions" class="position-relative mt-0"></div>

    <!-- Admin Dashboard Section -->
    <?php if (session('role') === 'admin'): ?>
        <div class="mb-4">
            <div class="row g-2">
                <div class="col-auto">
                    <a href="<?= base_url('admin/materials') ?>" class="btn btn-success btn-lg">
                        <i class="fas fa-folder-upload me-2"></i>Materials Management
                    </a>
                </div>
                <div class="col-auto">
                    <a href="<?= base_url('course/upload-schedule') ?>" class="btn btn-warning btn-lg">
                        <i class="fas fa-upload me-2"></i>Upload Schedule
                    </a>
                </div>
                <div class="col-auto">
                    <a href="<?= base_url('enrollment-dashboard') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>Enrollment Dashboard
                    </a>
                </div>
                <div class="col-auto">
                    <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-user-graduate me-2"></i>All Enrollments
                    </a>
                </div>
                <div class="col-auto">
                    <a href="<?= base_url('admin/schedule') ?>" class="btn btn-info btn-lg">
                        <i class="fas fa-calendar-alt me-2"></i>All Schedules
                    </a>
                </div>
                <div class="col-auto">
                    <a href="#" class="btn btn-secondary btn-lg">
                        <i class="fas fa-chart-bar me-2"></i>Analytics
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h3>System Statistics</h3>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Users</h5>
                            <h2><?= isset($totalUsers) ? $totalUsers : 0 ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Students</h5>
                            <h2><?= isset($studentCount) ? $studentCount : 0 ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Teachers</h5>
                            <h2><?= isset($teacherCount) ? $teacherCount : 0 ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Courses</h5>
                            <h2><?= isset($totalCourses) ? $totalCourses : 0 ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">
                                <i class="fas fa-user-graduate me-2"></i>Total Enrollments
                            </h5>
                            <h2 class="text-primary"><?= isset($totalEnrollments) ? $totalEnrollments : 0 ?></h2>
                            <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-sm btn-primary mt-2">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-success">
                        <div class="card-body text-center">
                            <h5 class="card-title text-success">
                                <i class="fas fa-users me-2"></i>Enrolled Students
                            </h5>
                            <h2 class="text-success"><?= isset($uniqueStudentsEnrolled) ? $uniqueStudentsEnrolled : 0 ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-info">
                        <div class="card-body text-center">
                            <h5 class="card-title text-info">
                                <i class="fas fa-calendar-alt me-2"></i>Courses with Enrollments
                            </h5>
                            <h2 class="text-info"><?= isset($coursesWithEnrollments) ? $coursesWithEnrollments : 0 ?></h2>
                            <a href="<?= base_url('admin/schedule') ?>" class="btn btn-sm btn-info mt-2">
                                View Schedule <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">All Courses</h3>
                <div>
                    <a href="<?= base_url('course/upload-schedule') ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-upload me-1"></i>Upload Schedule File
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Course Title</th>
                            <th>Description</th>
                            <th>Schedule</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allCourses)): ?>
                            <?php foreach ($allCourses as $course): ?>
                                <tr>
                                    <td><?= esc($course['title']) ?></td>
                                    <td><?= esc(substr($course['description'] ?? '', 0, 50)) ?>...</td>
                                    <td>
                                        <?php if (!empty($course['schedule_days'])): ?>
                                            <span class="badge bg-primary me-1">
                                                <i class="fas fa-calendar me-1"></i><?= esc($course['schedule_days']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($course['schedule_time_start'])): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('g:i A', strtotime($course['schedule_time_start'])) ?>
                                                <?php if (!empty($course['schedule_time_end'])): ?>
                                                    - <?= date('g:i A', strtotime($course['schedule_time_end'])) ?>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (empty($course['schedule_days']) && empty($course['schedule_time_start'])): ?>
                                            <span class="text-muted">Not scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($course['instructor_id'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="<?= base_url('course/edit-schedule/' . $course['id']) ?>" class="btn btn-sm btn-warning me-1" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('materials/list/' . $course['id']) ?>" class="btn btn-sm btn-primary" title="View Materials">
                                            <i class="fas fa-book"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No courses available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Recent Users</h3>
                <div class="list-group">
                    <?php if (!empty($recentUsers)): ?>
                        <?php foreach ($recentUsers as $user): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?= esc($user['name']) ?></h5>
                                    <small><span class="badge bg-secondary"><?= esc($user['role']) ?></span></small>
                                </div>
                                <p class="mb-1"><?= esc($user['email']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item">No users available.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Recent Enrollments</h3>
                <div class="list-group">
                    <?php if (!empty($recentEnrollments)): ?>
                        <?php 
                        $courseModel = new \App\Models\CourseModel();
                        $userModel = new \App\Models\UserModel();
                        foreach ($recentEnrollments as $enrollment): 
                            $course = $courseModel->find($enrollment['course_id']);
                            $student = $userModel->find($enrollment['user_id']);
                        ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($course['title'] ?? 'Unknown Course') ?></h6>
                                    <small class="text-muted"><?= date('M d', strtotime($enrollment['enrollment_date'])) ?></small>
                                </div>
                                <p class="mb-1">
                                    <i class="fas fa-user-graduate me-1"></i>
                                    <?= esc($student['name'] ?? 'Unknown Student') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item">No recent enrollments.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <!-- Teacher Dashboard Section -->
    <?php elseif (session('role') === 'teacher'): ?>
        <div class="mb-3">
            <a href="<?= base_url('schedule') ?>" class="btn btn-info btn-lg me-2">
                <i class="fas fa-calendar-alt me-2"></i>View Schedule
            </a>
            <a href="#" class="btn btn-success btn-lg">Create New Course</a>
        </div>
        
        <div class="mt-4">
            <h3>My Courses</h3>
            <div class="list-group" id="teacher-courses">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="list-group-item">
                            <h5 class="mb-1"><?= esc($course['title']) ?></h5>
                            <p class="mb-1"><?= esc($course['description']) ?></p>
                            <?php if (!empty($materials[$course['id']])): ?>
                                <div class="mt-3">
                                    <h6>Materials: <?= count($materials[$course['id']]) ?></h6>
                                    <a href="<?= base_url('materials/list/' . $course['id']) ?>" class="btn btn-primary btn-sm">Manage Materials</a>
                                    <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-success btn-sm">Upload Material</a>
                                </div>
                            <?php else: ?>
                                <div class="mt-3">
                                    <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-success btn-sm">Upload First Material</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-group-item">No courses created yet. <a href="#">Create one now</a></div>
                <?php endif; ?>
            </div>
        </div>

    <!-- Student Dashboard Section -->
    <?php else: ?>
        <!-- Quick Actions -->
        <div class="mb-3">
            <a href="<?= base_url('schedule') ?>" class="btn btn-info btn-lg">
                <i class="fas fa-calendar-alt me-2"></i>View My Schedule
            </a>
        </div>
        
        <!-- Enrolled Courses Section -->
        <div class="mt-4">
            <h3>Enrolled Courses</h3>
            <div class="list-group" id="enrolled-courses">
                <?php if (!empty($enrolledCourses)): ?>
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="list-group-item">
                            <h5 class="mb-1"><?= esc($course['title']) ?></h5>
                            <p class="mb-1"><?= esc($course['description']) ?></p>
                            <div class="mb-2">
                                <?php if (!empty($course['schedule_days'])): ?>
                                    <span class="badge bg-primary me-1">
                                        <i class="fas fa-calendar me-1"></i><?= esc($course['schedule_days']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($course['schedule_time_start'])): ?>
                                    <span class="badge bg-success me-1">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= date('g:i A', strtotime($course['schedule_time_start'])) ?>
                                        <?php if (!empty($course['schedule_time_end'])): ?>
                                            - <?= date('g:i A', strtotime($course['schedule_time_end'])) ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($course['schedule_location'])): ?>
                                    <span class="badge bg-danger me-1">
                                        <i class="fas fa-map-marker-alt me-1"></i><?= esc($course['schedule_location']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <small>Enrolled on: <?= esc($course['enrollment_date']) ?></small>
                            <?php if (!empty($materials[$course['id']])): ?>
                                <div class="mt-3">
                                    <h6>Materials:</h6>
                                    <a href="<?= base_url('materials/list/' . $course['id']) ?>" class="btn btn-primary btn-sm">View All Materials</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-group-item">No enrolled courses yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Available Courses Section -->
        <div class="mt-4">
            <h3>Available Courses</h3>
            <div class="row" id="available-courses">
                <?php if (!empty($availableCourses)): ?>
                    <?php foreach ($availableCourses as $course): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                    <p class="card-text flex-grow-1"><?= esc($course['description']) ?></p>
                                    <button type="button" class="btn btn-primary enroll-btn mt-auto" data-course-id="<?= $course['id'] ?>" style="cursor: pointer; z-index: 10;">Enroll</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No available courses.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <script>
        // Wait for DOM to be fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupEnrollment);
        } else {
            setupEnrollment();
        }

        function setupEnrollment() {
            console.log('Setting up enrollment buttons...');
            
            // Get all enroll buttons
            const enrollButtons = document.querySelectorAll('.enroll-btn');
            console.log('Found ' + enrollButtons.length + ' enroll buttons');
            
            if (enrollButtons.length === 0) {
                console.warn('No enroll buttons found on page');
                return;
            }
            
            enrollButtons.forEach((button, index) => {
                console.log('Attaching listener to button ' + (index + 1));
                
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    handleEnrollClick.call(this);
                });
            });
            
            console.log('Enrollment setup complete');
        }

        function handleEnrollClick() {
            console.log('=== ENROLL BUTTON CLICKED ===');
            const button = this;
            const courseId = button.getAttribute('data-course-id');
            
            console.log('Course ID from data attribute:', courseId);
            
            if (!courseId) {
                alert('Error: Could not find course ID');
                return;
            }
            
            // Disable button immediately
            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'Enrolling...';
            
            console.log('Sending enrollment request for course:', courseId);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const csrfHeader = 'X-CSRF-TOKEN'; // CodeIgniter default header name
            
            console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
            
            // Send POST request
            fetch('<?= base_url('course/enroll') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken && { [csrfHeader]: csrfToken })
                },
                body: 'course_id=' + encodeURIComponent(courseId)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    console.log('✓ Enrollment successful!');
                    
                    // Get course card details
                    const card = button.closest('.card');
                    const courseTitle = card.querySelector('.card-title').textContent.trim();
                    const courseDesc = card.querySelector('.card-text').textContent.trim();
                    const courseCol = button.closest('.col-md-4');
                    const currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
                    
                    console.log('Course details - Title:', courseTitle);
                    
                    // Update button
                    button.textContent = '✓ Enrolled';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-success');
                    button.disabled = true;
                    
                    // Create enrolled course HTML
                    const enrolledHtml = `
                        <div class="list-group-item">
                            <h5 class="mb-1">${courseTitle}</h5>
                            <p class="mb-1">${courseDesc}</p>
                            <small>Enrolled on: ${currentDate}</small>
                            <div class="mt-3">
                                <a href="<?= base_url('materials/list/') ?>${courseId}" class="btn btn-primary btn-sm">View Materials</a>
                            </div>
                        </div>
                    `;
                    
                    console.log('Enrolled HTML created');
                    
                    // Add to enrolled courses list
                    const enrolledList = document.getElementById('enrolled-courses');
                    console.log('Enrolled list element:', enrolledList ? 'Found' : 'Not found');
                    
                    if (enrolledList) {
                        // Remove "No enrolled courses yet" message
                        const noCoursesMsg = Array.from(enrolledList.querySelectorAll('.list-group-item')).find(item => 
                            item.textContent.trim() === 'No enrolled courses yet.'
                        );
                        
                        if (noCoursesMsg) {
                            console.log('Removing "no courses" message');
                            noCoursesMsg.remove();
                        }
                        
                        // Add new enrolled course at the top
                        console.log('Adding course to enrolled list');
                        enrolledList.insertAdjacentHTML('afterbegin', enrolledHtml);
                    }
                    
                    // Fade out and remove available course
                    console.log('Removing from available courses');
                    courseCol.style.transition = 'opacity 0.4s ease-out';
                    courseCol.style.opacity = '0';
                    
                    setTimeout(() => {
                        courseCol.remove();
                        
                        // Check if all courses are enrolled
                        const remainingCourses = document.querySelectorAll('#available-courses .col-md-4').length;
                        console.log('Remaining available courses:', remainingCourses);
                        
                        if (remainingCourses === 0) {
                            document.getElementById('available-courses').innerHTML = '<p class="text-muted">No available courses.</p>';
                        }
                    }, 400);
                    
                    // Show success message
                    showNotification('Success!', data.message, 'success');
                    
                } else {
                    console.error('Enrollment failed:', data.message);
                    showNotification('Error', data.message || 'Enrollment failed', 'danger');
                    button.disabled = false;
                    button.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                showNotification('Error', 'Network error: ' + error.message, 'danger');
                button.disabled = false;
                button.textContent = originalText;
            });
        }

        function showNotification(title, message, type) {
            console.log('Showing notification:', type, title, message);
            
            const alertClass = 'alert-' + type;
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert" 
                     style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 400px; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
                    <strong>${title}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.body.insertAdjacentHTML('afterbegin', alertHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                const alert = document.querySelector('.alert-' + type);
                if (alert) {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        }

        // Debug: Log when page loads
        console.log('Dashboard script loaded');
        console.log('Current URL:', window.location.href);

        // Initialize search autocomplete
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            console.log('Search input found, initializing autocomplete');
            let searchTimeout;
            
            searchInput.addEventListener('keyup', function() {
                console.log('Search input changed:', this.value);
                
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                const suggestionsDiv = document.getElementById('search-suggestions');
                
                // Clear suggestions if query is too short
                if (query.length < 1) {
                    suggestionsDiv.innerHTML = '';
                    return;
                }
                
                // Delay search to avoid too many requests
                searchTimeout = setTimeout(() => {
                    console.log('Performing search for:', query);
                    
                    fetch('<?= base_url('course/search') ?>?q=' + encodeURIComponent(query), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            console.log('Search response status:', response.status);
                            if (!response.ok) {
                                throw new Error('Search failed with status ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Search results:', data);
                            
                            if (data.success && data.results && data.results.length > 0) {
                                console.log('Found ' + data.results.length + ' courses');
                                
                                let suggestionsHtml = '<div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000; max-height: 400px; overflow-y: auto; top: 100%; left: 0;">';
                                
                                data.results.forEach(course => {
                                    const highlighted = course.title.replace(
                                        new RegExp(query, 'gi'),
                                        '<mark>$&</mark>'
                                    );
                                    
                                    suggestionsHtml += `
                                        <a href="#" class="list-group-item list-group-item-action search-suggestion" 
                                           data-course-id="${course.id}" data-course-title="${course.title}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <strong>${highlighted}</strong>
                                            </div>
                                            <small class="text-muted">${course.description.substring(0, 60)}...</small>
                                        </a>
                                    `;
                                });
                                
                                suggestionsHtml += '</div>';
                                suggestionsDiv.innerHTML = suggestionsHtml;
                                
                                // Attach click handlers to suggestions
                                document.querySelectorAll('.search-suggestion').forEach(item => {
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const courseTitle = this.getAttribute('data-course-title');
                                        searchInput.value = courseTitle;
                                        suggestionsDiv.innerHTML = '';
                                        document.getElementById('search-form').submit();
                                    });
                                });
                            } else if (data.success) {
                                console.log('No courses found');
                                suggestionsDiv.innerHTML = '<div class="alert alert-info mt-2">No courses found matching "' + query + '"</div>';
                            } else {
                                console.error('Search returned error:', data.message);
                                suggestionsDiv.innerHTML = '<div class="alert alert-warning mt-2">Error: ' + (data.message || 'Unknown error') + '</div>';
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            suggestionsDiv.innerHTML = '<div class="alert alert-danger mt-2">Error searching courses: ' + error.message + '</div>';
                        });
                }, 300);
            });
            
            // Clear suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#search-input') && !e.target.closest('#search-suggestions')) {
                    document.getElementById('search-suggestions').innerHTML = '';
                }
            });
        } else {
            console.warn('Search input not found');
        }
    </script>

<?= $this->endSection() ?>
