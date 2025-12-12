<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-tachometer-alt text-primary"></i> Enrollment Dashboard
        </h1>
        <div>
            <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-primary me-2">
                <i class="fas fa-list me-2"></i>View All Enrollments
            </a>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-user-graduate fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Enrollments</h5>
                    <h2 class="text-primary mb-0"><?= $totalEnrollments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-users fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title text-muted">Unique Students</h5>
                    <h2 class="text-success mb-0"><?= $uniqueStudents ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-book fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title text-muted">Courses with Enrollments</h5>
                    <h2 class="text-info mb-0"><?= $uniqueCourses ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-chart-line fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title text-muted">Average per Course</h5>
                    <h2 class="text-warning mb-0">
                        <?= $uniqueCourses > 0 ? number_format($totalEnrollments / $uniqueCourses, 1) : 0 ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Enrollment Trend Chart -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Enrollment Trend (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentChart" height="100"></canvas>
                </div>
            </div>

            <!-- Top Courses by Enrollment -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 5 Courses by Enrollment</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($topCourses)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Course</th>
                                        <th>Enrollments</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rank = 1;
                                    $maxEnrollments = max(array_column($topCourses, 'count'));
                                    foreach ($topCourses as $courseId => $courseData): 
                                        $percentage = $maxEnrollments > 0 ? ($courseData['count'] / $maxEnrollments) * 100 : 0;
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">#<?= $rank++ ?></span>
                                            </td>
                                            <td>
                                                <strong><?= esc($courseData['course_title']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?= $courseData['count'] ?> students</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: <?= $percentage ?>%" 
                                                         aria-valuenow="<?= $percentage ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?= $courseData['count'] ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No enrollment data available.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Enrollments</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentEnrollments)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Instructor</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentEnrollments as $enrollment): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-user-circle text-primary me-1"></i>
                                                <strong><?= esc($enrollment['student_name']) ?></strong>
                                                <br><small class="text-muted"><?= esc($enrollment['student_email']) ?></small>
                                            </td>
                                            <td>
                                                <i class="fas fa-book text-success me-1"></i>
                                                <?= esc($enrollment['title']) ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-tie text-info me-1"></i>
                                                <?= esc($enrollment['instructor_name']) ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar-check text-warning me-1"></i>
                                                <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?>
                                                <br><small class="text-muted"><?= date('g:i A', strtotime($enrollment['enrollment_date'])) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No recent enrollments.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Top Students -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Most Active Students</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($topStudents)): ?>
                        <div class="list-group list-group-flush">
                            <?php 
                            $rank = 1;
                            foreach ($topStudents as $studentId => $studentData): 
                            ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-warning text-dark me-2">#<?= $rank++ ?></span>
                                        <strong><?= esc($studentData['name']) ?></strong>
                                        <br><small class="text-muted"><?= esc($studentData['email']) ?></small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?= $studentData['count'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No student data available.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quick Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Enrollment Rate</span>
                            <strong><?= $uniqueStudents > 0 ? number_format(($totalEnrollments / $uniqueStudents), 1) : 0 ?> per student</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?= min(100, ($totalEnrollments / max($uniqueStudents, 1)) * 10) ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Course Coverage</span>
                            <strong><?= $uniqueCourses ?> courses</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= min(100, ($uniqueCourses / max($totalEnrollments, 1)) * 100) ?>%"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Last updated: <?= date('M d, Y g:i A') ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-list me-2"></i>View All Enrollments
                    </a>
                    <a href="<?= base_url('admin/schedule') ?>" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-calendar-alt me-2"></i>View Schedules
                    </a>
                    <a href="<?= base_url('courses') ?>" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-book me-2"></i>Manage Courses
                    </a>
                    <a href="<?= base_url('users') ?>" class="btn btn-warning w-100">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    // Enrollment Trend Chart
    const ctx = document.getElementById('enrollmentChart').getContext('2d');
    const enrollmentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?= implode(',', array_map(function($date) { return "'" . date('M d', strtotime($date)) . "'"; }, $last7Days)) ?>],
            datasets: [{
                label: 'Enrollments',
                data: [<?= implode(',', array_values($enrollmentsByDay)) ?>],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }
    .progress {
        height: 8px;
    }
    .list-group-item {
        border: none;
        border-bottom: 1px solid #dee2e6;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>

<?= $this->endSection() ?>

