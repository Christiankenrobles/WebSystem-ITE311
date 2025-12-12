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
                                <input type="file" class="form-control" id="material_file" name="material_file" required accept=".pdf,.ppt,.pptx">
                                <span class="input-group-text">
                                    <i class="fas fa-file"></i>
                                </span>
                            </div>
                            <small class="text-muted">
                                <strong>Supported formats:</strong> PDF, PPT, PPTX only
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
                    var extension = fileName.split('.').pop().toLowerCase();
                    var allowedExtensions = ['pdf', 'ppt', 'pptx'];
                    
                    // Validate file type immediately
                    if (!allowedExtensions.includes(extension)) {
                        alert('Invalid file type! Only PDF and PPT/PPTX files are allowed.\n\nYour file: ' + fileName + '\nDetected type: .' + extension);
                        $(this).val(''); // Clear file input
                        $('#file-preview').addClass('d-none');
                        return;
                    }
                    
                    // Check file size
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File size exceeds 10MB limit!');
                        $(this).val(''); // Clear file input
                        $('#file-preview').addClass('d-none');
                        return;
                    }
                    
                    $('#file-name').text(fileName);
                    $('#file-size').text(fileSize);
                    $('#file-preview').removeClass('d-none');
                } else {
                    $('#file-preview').addClass('d-none');
                }
            });

            $('#upload-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                
                var file = $('#material_file')[0].files[0];
                if (!file) {
                    alert('Please select a file to upload.');
                    return false;
                }
                
                var extension = file.name.split('.').pop().toLowerCase();
                var allowedExtensions = ['pdf', 'ppt', 'pptx'];
                
                if (!allowedExtensions.includes(extension)) {
                    alert('Only PDF and PPT/PPTX files are allowed.');
                    return false;
                }
                
                // Check file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size exceeds 10MB limit.');
                    return false;
                }
                
                // Show confirmation prompt
                var confirmMessage = 'Are you sure you want to upload this file?\n\n';
                confirmMessage += 'File: ' + file.name + '\n';
                confirmMessage += 'Size: ' + (file.size / 1024 / 1024).toFixed(2) + ' MB\n';
                confirmMessage += 'Course ID: <?= $course_id ?>';
                
                if (!confirm(confirmMessage)) {
                    return false;
                }
                
                // Disable submit button
                var $btn = $(this).find('button[type="submit"]');
                var originalText = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Uploading...');
                
                // Create FormData for AJAX upload
                var formData = new FormData(this);
                
                // AJAX upload
                $.ajax({
                    url: '<?= base_url('materials/upload/' . $course_id) ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_token() ?>'
                    },
                    success: function(response) {
                        console.log('Upload response:', response);
                        
                        // Handle both JSON and text responses
                        var data = response;
                        if (typeof response === 'string') {
                            try {
                                data = JSON.parse(response);
                            } catch (e) {
                                console.error('Failed to parse response:', e);
                                alert('Upload completed but response format error. Please refresh the page.');
                                $btn.prop('disabled', false).html(originalText);
                                return;
                            }
                        }
                        
                        if (data.success) {
                            // Show success notification
                            showSuccessNotification('Material uploaded successfully!');
                            
                            console.log('Material data:', data.material);
                            
                            // Always redirect to materials page to show updated list
                            setTimeout(function() {
                                window.location.href = '<?= base_url('admin/materials') ?>';
                            }, 1500);
                            
                            // Reset form
                            $('#upload-form')[0].reset();
                            $('#file-preview').addClass('d-none');
                            
                            // Re-enable button
                            $btn.prop('disabled', false).html(originalText);
                        } else {
                            alert('Upload failed: ' + (data.message || 'Unknown error'));
                            $btn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', xhr, status, error);
                        var errorMsg = 'Upload failed. ';
                        
                        // Try to parse error response
                        if (xhr.responseText) {
                            try {
                                var errorData = JSON.parse(xhr.responseText);
                                if (errorData.message) {
                                    errorMsg += errorData.message;
                                }
                            } catch (e) {
                                errorMsg += xhr.responseText.substring(0, 100);
                            }
                        } else {
                            errorMsg += error;
                        }
                        
                        alert(errorMsg);
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });
            
            // Success notification function
            function showSuccessNotification(message) {
                // Remove existing notifications
                $('.upload-notification').remove();
                
                // Create success notification
                var notification = $('<div class="alert alert-success alert-dismissible fade show upload-notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">' +
                    '<i class="fas fa-check-circle me-2"></i>' +
                    '<strong>Success!</strong> ' + message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                
                $('body').append(notification);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    notification.fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>

<?= $this->endSection() ?>