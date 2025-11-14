<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><?= esc($course['title']) ?></h2>
                <p class="text-muted"><?= esc($course['description']) ?></p>
            </div>
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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Course Materials
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($materials)): ?>
                <div class="list-group">
                    <?php foreach ($materials as $material): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div style="flex: 1;">
                                <h6 class="mb-1">
                                    <i class="fas fa-file me-2"></i><?= esc($material['file_name']) ?>
                                </h6>
                                <small class="text-muted">
                                    Uploaded on: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                </small>
                            </div>
                            <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm ms-2">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No materials available for this course yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Total Materials</h6>
                    <h2 class="text-primary"><?= count($materials) ?></h2>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Supported Formats</h6>
                    <p class="small">PDF, DOC, DOCX, TXT, JPT, PNG, MP4, AVI</p>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
