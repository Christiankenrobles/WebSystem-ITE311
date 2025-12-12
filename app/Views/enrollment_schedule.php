<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-calendar-alt text-primary"></i> My Enrollment Schedule
        </h1>
        <div>
            <?php if (session('role') === 'admin' || session('role') === 'teacher'): ?>
                <a href="<?= base_url('course/upload-schedule') ?>" class="btn btn-success me-2">
                    <i class="fas fa-upload me-2"></i>Upload Schedule File
                </a>
            <?php endif; ?>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <?php if (empty($enrollments)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            You are not enrolled in any courses yet. 
            <a href="<?= base_url('courses') ?>" class="alert-link">Browse available courses</a>
        </div>
    <?php else: ?>
        <!-- Summary Card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Total Enrollments</h5>
                        <h2 class="text-primary"><?= count($enrollments) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Scheduled Courses</h5>
                        <h2 class="text-success"><?= count(array_filter($enrollments, function($e) { return !empty($e['schedule_days']); })) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Online/Hybrid</h5>
                        <h2 class="text-info"><?= count(array_filter($enrollments, function($e) { return in_array($e['schedule_type'] ?? '', ['online', 'hybrid']); })) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Schedule View -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Weekly Schedule</h5>
            </div>
            <div class="card-body">
                <?php if (empty($scheduleByDay)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No schedule information available for your enrolled courses.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 15%;">Day</th>
                                    <th style="width: 20%;">Course</th>
                                    <th style="width: 15%;">Time</th>
                                    <th style="width: 15%;">Location</th>
                                    <th style="width: 10%;">Type</th>
                                    <th style="width: 15%;">Instructor</th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scheduleByDay as $day => $courses): ?>
                                    <?php if (count($courses) > 0): ?>
                                        <?php foreach ($courses as $index => $course): ?>
                                            <tr class="<?= $day === 'Unscheduled' ? 'table-secondary' : '' ?>">
                                                <?php if ($index === 0): ?>
                                                    <td rowspan="<?= count($courses) ?>" class="align-middle fw-bold text-center" style="vertical-align: middle;">
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
                                                    <?php if (session('role') === 'admin' || (session('role') === 'teacher' && session('user_id') == ($course['instructor_id'] ?? 0))): ?>
                                                        <a href="<?= base_url('course/edit-schedule/' . $course['id']) ?>" 
                                                           class="btn btn-sm btn-warning me-1" 
                                                           title="Edit Schedule">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('materials/list/' . $course['id']) ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="View Materials">
                                                        <i class="fas fa-book"></i>
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

        <!-- List View -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Enrolled Courses</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($enrollments as $enrollment): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-book text-primary me-2"></i>
                                        <?= esc($enrollment['title']) ?>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?= esc(substr($enrollment['description'] ?? 'No description', 0, 100)) ?>...
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
                                    <?php if (!empty($enrollment['instructor_name'])): ?>
                                        <p class="mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-user-tie me-1"></i>Instructor: <?= esc($enrollment['instructor_name']) ?>
                                            </small>
                                        </p>
                                    <?php endif; ?>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-check me-1"></i>Enrolled: <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?>
                                        </small>
                                    </p>
                                    <div class="mt-3">
                                        <?php if (session('role') === 'admin' || (session('role') === 'teacher' && session('user_id') == ($enrollment['instructor_id'] ?? 0))): ?>
                                            <a href="<?= base_url('course/edit-schedule/' . $enrollment['id']) ?>" 
                                               class="btn btn-sm btn-warning me-1">
                                                <i class="fas fa-edit me-1"></i>Edit Schedule
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('materials/list/' . $enrollment['id']) ?>" 
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

<?= $this->endSection() ?>

