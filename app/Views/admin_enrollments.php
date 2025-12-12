<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-user-graduate text-primary"></i> All Enrollments
        </h1>
        <div>
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
                    <h5 class="card-title text-muted">Unique Students</h5>
                    <h2 class="text-success"><?= $uniqueStudents ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Courses with Enrollments</h5>
                    <h2 class="text-info"><?= $uniqueCourses ?></h2>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($enrollments)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No enrollments found in the system.
        </div>
    <?php else: ?>
        <!-- All Enrollments Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Enrollments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Schedule</th>
                                <th>Location</th>
                                <th>Enrollment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($enrollment['student_name']) ?></strong>
                                        <br><small class="text-muted"><?= esc($enrollment['student_email']) ?></small>
                                        <br><span class="badge bg-secondary"><?= esc($enrollment['student_role']) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= esc($enrollment['course_title']) ?></strong>
                                        <?php if (!empty($enrollment['course_description'])): ?>
                                            <br><small class="text-muted"><?= esc(substr($enrollment['course_description'], 0, 50)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($enrollment['schedule_days'])): ?>
                                            <span class="badge bg-primary me-1">
                                                <i class="fas fa-calendar me-1"></i><?= esc($enrollment['schedule_days']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($enrollment['schedule_time_start'])): ?>
                                            <br>
                                            <span class="badge bg-success me-1">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('g:i A', strtotime($enrollment['schedule_time_start'])) ?>
                                                <?php if (!empty($enrollment['schedule_time_end'])): ?>
                                                    - <?= date('g:i A', strtotime($enrollment['schedule_time_end'])) ?>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (empty($enrollment['schedule_days']) && empty($enrollment['schedule_time_start'])): ?>
                                            <span class="text-muted">Not scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($enrollment['schedule_location'])): ?>
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            <?= esc($enrollment['schedule_location']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar-check text-primary me-1"></i>
                                        <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?>
                                        <br><small class="text-muted"><?= date('g:i A', strtotime($enrollment['enrollment_date'])) ?></small>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('materials/list/' . $enrollment['course_id']) ?>" 
                                           class="btn btn-sm btn-primary" 
                                           title="View Course Materials">
                                            <i class="fas fa-book"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Enrollments by Course -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Enrollments by Course</h5>
            </div>
            <div class="card-body">
                <?php foreach ($enrollmentsByCourse as $courseData): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-book text-primary me-2"></i>
                                <strong><?= esc($courseData['course']['title']) ?></strong>
                                <span class="badge bg-primary ms-2"><?= count($courseData['enrollments']) ?> students</span>
                            </h6>
                            <?php if (!empty($courseData['course']['schedule_days'])): ?>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i><?= esc($courseData['course']['schedule_days']) ?>
                                    <?php if (!empty($courseData['course']['schedule_time_start'])): ?>
                                        | <i class="fas fa-clock me-1"></i>
                                        <?= date('g:i A', strtotime($courseData['course']['schedule_time_start'])) ?>
                                        <?php if (!empty($courseData['course']['schedule_time_end'])): ?>
                                            - <?= date('g:i A', strtotime($courseData['course']['schedule_time_end'])) ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($courseData['course']['schedule_location'])): ?>
                                        | <i class="fas fa-map-marker-alt me-1"></i><?= esc($courseData['course']['schedule_location']) ?>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($courseData['enrollments'] as $enrollment): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                            <div>
                                                <strong><?= esc($enrollment['student_name']) ?></strong>
                                                <br><small class="text-muted"><?= esc($enrollment['student_email']) ?></small>
                                                <br><small class="text-muted">
                                                    Enrolled: <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>

<?= $this->endSection() ?>

