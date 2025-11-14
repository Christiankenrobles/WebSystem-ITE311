<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Upload Course Material</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error!</strong> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('materials/upload/' . $course_id) ?>" method="post" enctype="multipart/form-data" id="upload-form">
                        <div class="mb-4">
                            <label for="material_file" class="form-label fw-bold">Select File to Upload</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="material_file" name="material_file" required accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.mp4,.avi">
                                <span class="input-group-text">
                                    <i class="fas fa-file"></i>
                                </span>
                            </div>
                            <small class="text-muted">
                                <strong>Supported formats:</strong> PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, MP4, AVI
                                <br><strong>Max file size:</strong> 10MB
                            </small>
                        </div>

                        <div class="mb-4">
                            <div id="file-preview" class="alert alert-info d-none">
                                <i class="fas fa-info-circle me-2"></i>
                                Selected file: <strong id="file-name"></strong>
                                <br>
                                File size: <strong id="file-size"></strong>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload me-2"></i>Upload Material
                            </button>
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning mt-4" role="alert">
                <i class="fas fa-lightbulb me-2"></i>
                <strong>Tip:</strong> Once uploaded, this material will be available to all students enrolled in course <?= $course_id ?>.
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#material_file').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var fileName = file.name;
                    var fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    
                    $('#file-name').text(fileName);
                    $('#file-size').text(fileSize);
                    $('#file-preview').removeClass('d-none');
                } else {
                    $('#file-preview').addClass('d-none');
                }
            });

            $('#upload-form').on('submit', function() {
                var $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Uploading...');
            });
        });
    </script>

<?= $this->endSection() ?>