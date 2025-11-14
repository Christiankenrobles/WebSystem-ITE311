<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Materials Management</h2>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
        <hr>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Total Courses</h5>
                    <h2 class="text-primary"><?= count($allCourses) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Total Materials</h5>
                    <h2 class="text-success"><?= count($allMaterials) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Courses with Materials</h5>
                    <h2 class="text-info"><?= count($coursesWithMaterials) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Courses without Materials</h5>
                    <h2 class="text-warning"><?= count($allCourses) - count($coursesWithMaterials) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses with Materials Management -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-book me-2"></i>Manage Course Materials
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($allCourses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Course Title</th>
                                <th>Description</th>
                                <th>Materials Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allCourses as $course): ?>
                                <?php 
                                    $courseMaterialCount = isset($materialsByCourseByCourse[$course['id']]) 
                                        ? count($materialsByCourseByCourse[$course['id']]) 
                                        : 0;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($course['title']) ?></strong>
                                    </td>
                                    <td>
                                        <small><?= esc(substr($course['description'], 0, 50)) ?>...</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $courseMaterialCount ?> files</span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-upload me-1"></i>Upload
                                        </a>
                                        <a href="<?= base_url('materials/list/' . $course['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-list me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No courses available yet. Create a course first to upload materials.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Materials -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Recently Uploaded Materials
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($recentMaterials)): ?>
                <div class="list-group">
                    <?php foreach ($recentMaterials as $material): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-file me-2"></i><?= esc($material['file_name']) ?>
                                    </h6>
                                    <small class="text-muted">
                                        Course ID: <?= $material['course_id'] ?> | 
                                        Uploaded: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                    </small>
                                </div>
                                <div>
                                    <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                    <a href="<?= base_url('materials/delete/' . $material['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No materials uploaded yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

<?= $this->endSection() ?>
