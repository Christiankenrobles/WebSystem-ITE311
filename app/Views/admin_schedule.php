<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-calendar-alt text-primary"></i> All Schedules
        </h1>
        <div>
            <a href="<?= base_url('course/upload-schedule') ?>" class="btn btn-success me-2">
                <i class="fas fa-upload me-2"></i>Upload Schedule File
            </a>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Total Enrollments</h5>
                    <h2 class="text-primary"><?= $totalEnrollments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Courses with Schedule</h5>
                    <h2 class="text-success"><?= $coursesWithSchedule ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Scheduled Days</h5>
                    <h2 class="text-info"><?= count(array_filter($scheduleByDay, function($day) { return $day !== 'Unscheduled'; }, ARRAY_FILTER_USE_KEY)) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($enrollments)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No enrollments with schedule information found.
        </div>
    <?php else: ?>
        <!-- Weekly Schedule View -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Weekly Schedule Overview</h5>
            </div>
            <div class="card-body">
                <?php if (empty($scheduleByDay)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No schedule information available.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 12%;">Day</th>
                                    <th style="width: 20%;">Course</th>
                                    <th style="width: 12%;">Time</th>
                                    <th style="width: 15%;">Location</th>
                                    <th style="width: 10%;">Type</th>
                                    <th style="width: 15%;">Instructor</th>
                                    <th style="width: 16%;">Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scheduleByDay as $day => $courses): ?>
                                    <?php if (count($courses) > 0): ?>
                                        <?php 
                                        // Group by course (same course, multiple students)
                                        $groupedCourses = [];
                                        foreach ($courses as $course) {
                                            $key = $course['course_id'] . '_' . ($course['schedule_time_start'] ?? '');
                                            if (!isset($groupedCourses[$key])) {
                                                $groupedCourses[$key] = [
                                                    'course' => $course,
                                                    'students' => []
                                                ];
                                            }
                                            $groupedCourses[$key]['students'][] = $course['student_name'];
                                        }
                                        ?>
                                        <?php foreach ($groupedCourses as $index => $group): ?>
                                            <?php $course = $group['course']; ?>
                                            <tr class="<?= $day === 'Unscheduled' ? 'table-secondary' : '' ?>">
                                                <?php if ($index === array_key_first($groupedCourses)): ?>
                                                    <td rowspan="<?= count($groupedCourses) ?>" class="align-middle fw-bold text-center" style="vertical-align: middle;">
                                                        <?php if ($day === 'Unscheduled'): ?>
                                                            <span class="badge bg-secondary">Unscheduled</span>
                                                        <?php else: ?>
                                                            <?= $day ?>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                                <td>
                                                    <strong><?= esc($course['title']) ?></strong>
                                                    <?php if (!empty($course['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($course['description'], 0, 50)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($course['schedule_time_start']) && !empty($course['schedule_time_end'])): ?>
                                                        <i class="fas fa-clock text-primary me-1"></i>
                                                        <?= date('g:i A', strtotime($course['schedule_time_start'])) ?> - 
                                                        <?= date('g:i A', strtotime($course['schedule_time_end'])) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not set</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($course['schedule_location'])): ?>
                                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                        <?= esc($course['schedule_location']) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $type = $course['schedule_type'] ?? 'regular';
                                                    $badgeClass = [
                                                        'online' => 'bg-info',
                                                        'hybrid' => 'bg-warning',
                                                        'regular' => 'bg-primary'
                                                    ][$type] ?? 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= ucfirst($type) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($course['instructor_name'])): ?>
                                                        <i class="fas fa-user-tie text-success me-1"></i>
                                                        <?= esc($course['instructor_name']) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?= count($group['students']) ?> student(s)</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" 
                                                            data-bs-toggle="popover" 
                                                            data-bs-trigger="focus"
                                                            data-bs-title="Enrolled Students"
                                                            data-bs-content="<?= htmlspecialchars(implode(', ', $group['students'])) ?>">
                                                        <i class="fas fa-users"></i> View
                                                    </button>
                                                    <br>
                                                    <a href="<?= base_url('course/edit-schedule/' . $course['course_id']) ?>" 
                                                       class="btn btn-sm btn-warning mt-1" 
                                                       title="Edit Schedule">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detailed Schedule List -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detailed Schedule List</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($enrollments as $enrollment): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-book text-primary me-2"></i>
                                        <?= esc($enrollment['title']) ?>
                                    </h6>
                                    <p class="card-text text-muted small">
                                        <?= esc(substr($enrollment['description'] ?? 'No description', 0, 80)) ?>...
                                    </p>
                                    <div class="mb-2">
                                        <?php if (!empty($enrollment['schedule_days'])): ?>
                                            <span class="badge bg-primary me-1">
                                                <i class="fas fa-calendar me-1"></i><?= esc($enrollment['schedule_days']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($enrollment['schedule_time_start'])): ?>
                                            <span class="badge bg-success me-1">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('g:i A', strtotime($enrollment['schedule_time_start'])) ?>
                                                <?php if (!empty($enrollment['schedule_time_end'])): ?>
                                                    - <?= date('g:i A', strtotime($enrollment['schedule_time_end'])) ?>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($enrollment['schedule_location'])): ?>
                                            <span class="badge bg-danger me-1">
                                                <i class="fas fa-map-marker-alt me-1"></i><?= esc($enrollment['schedule_location']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($enrollment['schedule_type'])): ?>
                                            <span class="badge bg-info">
                                                <?= ucfirst($enrollment['schedule_type']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user-tie me-1"></i><strong>Instructor:</strong> 
                                            <?= esc($enrollment['instructor_name'] ?? 'N/A') ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-user-graduate me-1"></i><strong>Student:</strong> 
                                            <?= esc($enrollment['student_name']) ?>
                                        </small>
                                    </div>
                                    <div class="mt-2">
                                        <a href="<?= base_url('course/edit-schedule/' . $enrollment['course_id']) ?>" 
                                           class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit me-1"></i>Edit Schedule
                                        </a>
                                        <a href="<?= base_url('materials/list/' . $enrollment['course_id']) ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-book me-1"></i>View Materials
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .table td {
        vertical-align: middle;
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    // Initialize popovers
    document.addEventListener('DOMContentLoaded', function() {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
</script>

<?= $this->endSection() ?>

