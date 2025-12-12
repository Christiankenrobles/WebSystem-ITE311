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
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label for="material_file" class="form-label fw-bold">Select File to Upload</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="material_file" name="material_file" required accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation">
                                <span class="input-group-text">
                                    <i class="fas fa-file"></i>
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <strong>Supported formats:</strong> PDF, PPT, PPTX only
                                <br><strong>Max file size:</strong> 10MB
                            </small>
                            <div id="file-error" class="invalid-feedback"></div>
                        </div>

                        <div class="mb-4">
                            <div id="file-preview" class="alert alert-info d-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file me-2"></i>
                                        <strong id="file-name"></strong>
                                        <div class="text-muted small mt-1">
                                            Size: <span id="file-size"></span> • 
                                            Type: <span id="file-type"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" id="clear-file"></button>
                                </div>
                                <div class="progress mt-2 d-none" id="upload-progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <i class="fas fa-upload me-2"></i>Upload Material
                            </button>
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning mt-4" role="alert">
                <i class="fas fa-lightbulb me-2"></i>
                <strong>Tip:</strong> Once uploaded, this material will be available to all students enrolled in course <strong><?= esc($course['title'] ?? '') ?></strong>.
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Supported file types and their icons
            const fileIcons = {
                'pdf': 'file-pdf',
                'ppt': 'file-powerpoint',
                'pptx': 'file-powerpoint'
            };
            
            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
            
            // Get file icon
            function getFileIcon(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                return fileIcons[ext] || 'file';
            }
            
            // Validate file
            function validateFile(file) {
                const allowedTypes = ['application/pdf', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'];
                const allowedExtensions = ['pdf', 'ppt', 'pptx'];
                const maxSize = 10 * 1024 * 1024; // 10MB
                
                const fileExt = file.name.split('.').pop().toLowerCase();
                const isValidType = allowedTypes.includes(file.type) || allowedExtensions.includes(fileExt);
                const isValidSize = file.size <= maxSize;
                
                return {
                    isValid: isValidType && isValidSize,
                    errors: {
                        type: !isValidType ? 'Invalid file type. Only PDF and PPT/PPTX files are allowed.' : null,
                        size: !isValidSize ? 'File size exceeds 10MB limit.' : null
                    }
                };
            }
            
            // Handle file selection
            $('#material_file').on('change', function() {
                const fileInput = this;
                const file = fileInput.files[0];
                const $filePreview = $('#file-preview');
                const $fileError = $('#file-error');
                
                if (file) {
                    const validation = validateFile(file);
                    
                    if (!validation.isValid) {
                        // Show error message
                        const errorMsg = Object.values(validation.errors).filter(Boolean).join(' ');
                        $fileError.text(errorMsg);
                        $(fileInput).addClass('is-invalid');
                        $filePreview.addClass('d-none');
                        return;
                    }
                    
                    // Clear any previous errors
                    $fileError.text('');
                    $(fileInput).removeClass('is-invalid');
                    
                    // Update file preview
                    const fileIcon = getFileIcon(file.name);
                    $('#file-name').html(`<i class="fas fa-${fileIcon} me-2"></i>${file.name}`);
                    $('#file-size').text(formatFileSize(file.size));
                    $('#file-type').text(file.type || 'Unknown type');
                    
                    // Show preview
                    $filePreview.removeClass('d-none');
                } else {
                    $filePreview.addClass('d-none');
                }
            });

            // Clear file selection
            $('#clear-file').on('click', function() {
                $('#material_file').val('');
                $('#file-preview').addClass('d-none');
                $('#file-error').text('');
                $('#material_file').removeClass('is-invalid');
            });
            
            // Handle form submission
            $('#upload-form').on('submit', function(e) {
                e.preventDefault();
                
                const fileInput = $('#material_file')[0];
                const file = fileInput.files[0];
                
                if (!file) {
                    $('#file-error').text('Please select a file to upload');
                    $(fileInput).addClass('is-invalid');
                    return;
                }
                
                // Validate file again before upload
                const validation = validateFile(file);
                if (!validation.isValid) {
                    const errorMsg = Object.values(validation.errors).filter(Boolean).join(' ');
                    $('#file-error').text(errorMsg);
                    $(fileInput).addClass('is-invalid');
                    return;
                }
                
                const formData = new FormData(this);
                const $submitBtn = $('#submit-btn');
                const originalBtnText = $submitBtn.html();
                const $progress = $('#upload-progress');
                const $progressBar = $progress.find('.progress-bar');
                
                // Update UI for upload in progress
                $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Uploading...');
                $progress.removeClass('d-none');
                $progressBar.css('width', '0%').attr('aria-valuenow', 0);
                
                // AJAX upload with progress tracking
                $.ajax({
                    url: '<?= base_url('materials/upload/' . $course_id) ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                $progressBar.css('width', percentComplete + '%')
                                           .attr('aria-valuenow', percentComplete)
                                           .text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        if (response.success) {
                            $progressBar.removeClass('bg-warning').addClass('bg-success');
                            showSuccessNotification('File uploaded successfully!');
                            
                            // Redirect to materials list after a short delay
                            setTimeout(function() {
                                window.location.href = '<?= base_url('materials/list/' . $course_id) ?>';
                            }, 1000);
                        } else {
                            throw new Error(response.message || 'Upload failed');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred during upload. Please try again.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        $progressBar.removeClass('bg-primary').addClass('bg-danger');
                        $progressBar.text('Upload Failed');
                        $submitBtn.prop('disabled', false).html(originalBtnText);
                        
                        // Show error message
                        const $errorAlert = $(`
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${errorMsg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                        $('.card-body').prepend($errorAlert);
                        
                        // Auto-dismiss error after 5 seconds
                        setTimeout(() => {
                            $errorAlert.alert('close');
                        }, 5000);
                    }
                });
            });
            
            // Success notification function
            function showSuccessNotification(message) {
                // Remove existing notifications
                $('.toast').remove();
                
                // Create and show success toast
                const toast = `
                    <div class="toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i>
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                
                $('body').append(toast);
                const toastElement = $('.toast');
                toastElement.toast({ delay: 3000 });
                
                // Add animation classes
                toastElement.addClass('animate__animated animate__fadeInUp');
                toastElement.on('hidden.bs.toast', function () {
                    $(this).remove();
                });
                
                toastElement.toast('show');
            }
        });
    </script>

<?= $this->endSection() ?>