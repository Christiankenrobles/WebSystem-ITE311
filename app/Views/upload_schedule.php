<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-upload text-primary"></i> Upload Schedule File
        </h1>
        <div>
            <a href="<?= base_url('admin/schedule') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Schedule
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Upload Schedule File (CSV/Excel)</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>


                    <!-- Upload Form -->
                    <form method="POST" action="<?= base_url('course/upload-schedule') ?>" enctype="multipart/form-data" id="uploadForm">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="schedule_file" class="form-label fw-bold">
                                <i class="fas fa-file me-1"></i>Select Schedule File
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="schedule_file" 
                                   name="schedule_file" 
                                   accept=".csv,.txt,.xlsx,.xls"
                                   required>
                            <small class="form-text text-muted">
                                Supported formats: CSV, TXT, Excel (XLSX, XLS). Max file size: 5MB
                            </small>
                        </div>

                        <div id="file-preview" class="alert alert-info d-none mb-3">
                            <i class="fas fa-file me-2"></i>
                            Selected file: <strong id="file-name"></strong>
                            <br>
                            File size: <strong id="file-size"></strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/schedule') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload and Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // File preview
    document.getElementById('schedule_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const preview = document.getElementById('file-preview');
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = (file.size / 1024).toFixed(2) + ' KB';
            preview.classList.remove('d-none');
        }
    });

    // Form validation
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('schedule_file');
        const file = fileInput.files[0];
        
        if (!file) {
            e.preventDefault();
            alert('Please select a file to upload');
            return false;
        }

        // Check file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            e.preventDefault();
            alert('File size exceeds 5MB limit');
            return false;
        }

        // Check file extension
        const allowedExtensions = ['csv', 'txt', 'xlsx', 'xls'];
        const extension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(extension)) {
            e.preventDefault();
            alert('Invalid file type. Please upload CSV, TXT, or Excel file.');
            return false;
        }
    });
</script>

<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>

<?= $this->endSection() ?>

