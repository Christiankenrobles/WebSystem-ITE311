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
            <?php elseif (session('role') === 'instructor'): ?>
                <p class="mb-0">Instructor Dashboard: Create and manage courses and lessons.</p>
            <?php else: ?>
                <p class="mb-0">Student Dashboard: Enroll in courses and take quizzes.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session('role') === 'student'): ?>
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
                                    <button class="btn btn-primary enroll-btn" data-course-id="<?= $course['id'] ?>">Enroll</button>
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
            $('.enroll-btn').on('click', function(e) {
                e.preventDefault();
                var courseId = $(this).data('course-id');
                var button = $(this);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(response) {
                    if (response.success) {
                        // Show success alert
                        var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>';
                        $('.container').prepend(alertHtml);

                        // Hide or disable the enroll button
                        button.prop('disabled', true).text('Enrolled');

                        // Update enrolled courses list dynamically
                        var enrolledList = $('#enrolled-courses');
                        var courseCard = button.closest('.card');
                        var courseTitle = courseCard.find('.card-title').text();
                        var courseDesc = courseCard.find('.card-text').text();
                        var currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');

                        var newEnrolledItem = '<div class="list-group-item">' +
                            '<h5 class="mb-1">' + courseTitle + '</h5>' +
                            '<p class="mb-1">' + courseDesc + '</p>' +
                            '<small>Enrolled on: ' + currentDate + '</small>' +
                            '</div>';

                        if (enrolledList.find('.list-group-item').first().text() === 'No enrolled courses yet.') {
                            enrolledList.empty();
                        }
                        enrolledList.append(newEnrolledItem);

                        // Remove the course from available courses
                        courseCard.parent().remove();
                    } else {
                        // Show error alert
                        var alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>';
                        $('.container').prepend(alertHtml);
                    }
                }, 'json');
            });
        });
    </script>
<?= $this->endSection() ?>
