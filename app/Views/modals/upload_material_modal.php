<!-- Upload Material Modal -->
<div class="modal fade" id="uploadMaterialModal" tabindex="-1" aria-labelledby="uploadMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadMaterialModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Course Material
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="upload-status" class="mb-3"></div>
                
                <form id="material-upload-form" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="course_id" id="upload-course-id">
                    
                    <div class="mb-4">
                        <label for="material_file" class="form-label fw-bold">
                            <i class="fas fa-file me-2"></i>Select File to Upload
                        </label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="material_file" name="material_file" required 
                                   accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation">
                        </div>
                        <small class="text-muted d-block mt-1">
                            <strong>Supported formats:</strong> PDF, PPT, PPTX only (Max: 10MB)
                        </small>
                        <div id="file-error" class="invalid-feedback"></div>
                    </div>
                    
                    <div id="file-preview" class="alert alert-info d-none mb-3">
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
                        <div class="progress mt-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" aria-valuenow="0" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 pt-4 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-upload">
                            <i class="fas fa-upload me-2"></i>Upload Material
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
#uploadMaterialModal .modal-content {
    border: none;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

#uploadMaterialModal .modal-header {
    border-radius: 9px 9px 0 0;
    padding: 1.2rem 1.5rem;
}

#uploadMaterialModal .modal-body {
    padding: 1.5rem;
}

#uploadMaterialModal .form-control {
    padding: 0.75rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
}

#uploadMaterialModal .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

#file-preview {
    border-left: 4px solid #0d6efd;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: #f0f0f0;
}

.progress-bar {
    transition: width 0.3s ease;
}

#upload-status {
    min-height: 30px;
}
</style>
