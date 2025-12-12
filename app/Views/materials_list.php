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
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Course Materials
            </h5>
            <?php if (isset($canUpload) && $canUpload): ?>
                <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-upload me-1"></i>Upload Material
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php 
            $materialsCount = is_array($materials) ? count($materials) : 0;
            if ($materialsCount > 0): ?>
                <div class="list-group" id="materials-list">
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
                            <div>
                                <?php if (isset($canUpload) && $canUpload): ?>
                                    <a href="<?= base_url('materials/delete/' . $material['id']) ?>" class="btn btn-danger btn-sm me-1" onclick="return confirm('Are you sure you want to delete this material?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No materials available for this course yet.
                    <?php if (isset($canUpload) && $canUpload): ?>
                        <br>
                        <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-upload me-1"></i>Upload First Material
                        </a>
                    <?php endif; ?>
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
                    <h2 class="text-primary" id="total-materials-count"><?= isset($materials) && is_array($materials) ? count($materials) : 0 ?></h2>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Supported Formats</h6>
                    <p class="small">PDF, PPT, PPTX only</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-update materials count immediately on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateMaterialsCount();
            
            // If there's a success message (from upload), ensure count is updated
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                // Update count immediately
                updateMaterialsCount();
                
                // Also update after a short delay to ensure all DOM elements are ready
                setTimeout(updateMaterialsCount, 200);
            }
        });
        
        function updateMaterialsCount() {
            const materialsList = document.getElementById('materials-list');
            const countElement = document.getElementById('total-materials-count');
            
            if (materialsList && countElement) {
                // Count actual material items in the list
                const materialItems = materialsList.querySelectorAll('.list-group-item');
                const count = materialItems.length;
                countElement.textContent = count;
                
                // Also update the count with animation
                countElement.style.transition = 'all 0.3s ease';
                countElement.style.transform = 'scale(1.1)';
                setTimeout(function() {
                    countElement.style.transform = 'scale(1)';
                }, 300);
            } else if (countElement) {
                // If no materials list exists, check if we have materials from server
                const materialsCount = <?= isset($materials) && is_array($materials) ? count($materials) : 0 ?>;
                countElement.textContent = materialsCount;
            }
        }
    </script>

<?= $this->endSection() ?>
