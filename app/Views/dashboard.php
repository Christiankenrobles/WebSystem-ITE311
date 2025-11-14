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
                    <a href="#" class="btn btn-info btn-lg">
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
        </div>

        <div class="mt-4">
            <h3>All Courses</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Course Title</th>
                            <th>Description</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allCourses)): ?>
                            <?php foreach ($allCourses as $course): ?>
                                <tr>
                                    <td><?= esc($course['title']) ?></td>
                                    <td><?= esc(substr($course['description'], 0, 50)) ?>...</td>
                                    <td><?= esc($course['instructor_id'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No courses available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <h3>Recent Users</h3>
            <div class="list-group">
                <?php if (!empty($recentUsers)): ?>
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?= esc($user['name']) ?></h5>
                                <small><?= esc($user['role']) ?></small>
                            </div>
                            <p class="mb-1"><?= esc($user['email']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-group-item">No users available.</div>
                <?php endif; ?>
            </div>
        </div>

    <!-- Teacher Dashboard Section -->
    <?php elseif (session('role') === 'teacher'): ?>
        <div class="mt-4">
            <h3>My Courses</h3>
            <a href="#" class="btn btn-success mb-3">Create New Course</a>
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
        <!-- Enrolled Courses Section -->
        <div class="mt-4">
            <h3>Enrolled Courses</h3>
            <div class="list-group" id="enrolled-courses">
                <?php if (!empty($enrolledCourses)): ?>
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="list-group-item">
                            <h5 class="mb-1"><?= esc($course['title']) ?></h5>
                            <p class="mb-1"><?= esc($course['description']) ?></p>
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
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                    <p class="card-text"><?= esc($course['description']) ?></p>
                                    <button type="button" class="btn btn-primary enroll-btn" data-course-id="<?= $course['id'] ?>">Enroll</button>
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
        $(document).ready(function() {
            console.log('Dashboard script loaded');
            
            // Handle enrollment button clicks
            $(document).on('click', '.enroll-btn', function(e) {
                console.log('Enroll button clicked');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                var $button = $(this);
                var courseId = $button.data('course-id');
                
                console.log('Course ID:', courseId);
                
                if (!courseId) {
                    alert('Invalid course ID');
                    return false;
                }
                
                // Disable button and show loading state
                $button.prop('disabled', true);
                var originalText = $button.text();
                $button.text('Enrolling...');
                
                // Make AJAX request
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('course/enroll') ?>',
                    dataType: 'json',
                    data: {
                        course_id: courseId
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        
                        if (response.success) {
                            // Get course details
                            var $card = $button.closest('.card');
                            var courseTitle = $card.find('.card-title').text();
                            var courseDesc = $card.find('.card-text').text();
                            var $courseCol = $button.closest('.col-md-4');
                            var currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
                            
                            // Show success notification
                            showNotification('Success', response.message, 'success');
                            
                            // Update button
                            $button.html('✓ Enrolled').removeClass('btn-primary').addClass('btn-success').prop('disabled', true);
                            
                            // Add to enrolled courses
                            var enrolledHtml = '<div class="list-group-item">' +
                                '<h5 class="mb-1">' + courseTitle + '</h5>' +
                                '<p class="mb-1">' + courseDesc + '</p>' +
                                '<small>Enrolled on: ' + currentDate + '</small>' +
                                '<div class="mt-3">' +
                                '<a href="<?= base_url('materials/list/') ?>' + courseId + '" class="btn btn-primary btn-sm">View Materials</a>' +
                                '</div>' +
                                '</div>';
                            
                            var $enrolledList = $('#enrolled-courses');
                            
                            // Remove "No enrolled courses" message if it exists
                            $enrolledList.find('.list-group-item:contains("No enrolled courses")').remove();
                            
                            // Add new enrolled course
                            $enrolledList.append(enrolledHtml);
                            
                            // Remove from available courses
                            $courseCol.fadeOut(400, function() {
                                $(this).remove();
                                
                                // Check if available courses section is empty
                                var remainingCourses = $('#available-courses .col-md-4').length;
                                if (remainingCourses === 0) {
                                    $('#available-courses').html('<p class="text-muted">No available courses.</p>');
                                }
                            });
                        } else {
                            showNotification('Info', response.message, 'warning');
                            $button.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown, jqXHR);
                        
                        var errorMsg = 'An error occurred. Please try again.';
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMsg = jqXHR.responseJSON.message;
                        }
                        
                        showNotification('Error', errorMsg, 'danger');
                        $button.prop('disabled', false).text(originalText);
                    }
                });
                
                return false;
            });
            
            // Helper function to show notifications
            function showNotification(title, message, type) {
                var alertClass = 'alert-' + type;
                var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 350px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
                    '<strong>' + title + ':</strong> ' + message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>';
                
                $('body').prepend(alertHtml);
                
                setTimeout(function() {
                    $('.alert-' + type).fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 4000);
            }
        });
    </script>
<?= $this->endSection() ?>
