<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Materials Management</h2>
            <div>
                <button onclick="window.location.reload()" class="btn btn-outline-primary me-2">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
        <hr>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" id="success-alert" style="animation: slideDown 0.5s ease-out;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">Upload Successful!</h5>
                    <p class="mb-0"><?= session()->getFlashdata('success') ?></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <script>
            // Show notification and reload page
            console.log('Upload successful, showing notification...');
            
            // Play notification sound (optional)
            // var audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZURAJR6Hh8sBrJAUwgM/z1oQ5CBxrvO3mnlEQDE+n4fC2YxwGOJLX8sx5LAUkd8fw3ZBACxRetOnrqFUUCkaf4PK+bCEFMYfR89OCMwYebsDv45lREAlHoeHywGskBTCAz/PWhDkIHGu87eaeURAMT6fh8LZjHAY4ktfy');
            // audio.play().catch(e => console.log('Audio play failed:', e));
            
            // Show toast notification
            if (typeof bootstrap !== 'undefined') {
                var toastEl = document.createElement('div');
                toastEl.className = 'toast align-items-center text-white bg-success border-0';
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check-circle me-2"></i>
                            Material uploaded successfully!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toastEl);
                var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                toast.show();
            }
            
            // Force page reload after 2 seconds to show updated counts and recent materials
            setTimeout(function() {
                var timestamp = new Date().getTime();
                window.location.href = '<?= base_url('admin/materials') ?>?t=' + timestamp;
            }, 2000);
        </script>
        <style>
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
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
                                    // Get fresh material count for this course
                                    $courseMaterialCount = 0;
                                    if (isset($materialsByCourseByCourse[$course['id']]) && is_array($materialsByCourseByCourse[$course['id']])) {
                                        $courseMaterialCount = count($materialsByCourseByCourse[$course['id']]);
                                    } else {
                                        // Fallback: query directly if not in array
                                        $materialModel = new \App\Models\MaterialModel();
                                        $directMaterials = $materialModel->where('course_id', $course['id'])->findAll();
                                        $courseMaterialCount = count($directMaterials);
                                    }
                                    
                                    // Debug: Log course info
                                    // echo "<!-- Course ID: " . $course['id'] . ", Title: " . $course['title'] . ", Count: " . $courseMaterialCount . " -->";
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($course['title']) ?></strong>
                                        <small class="text-muted d-block">ID: <?= $course['id'] ?></small>
                                    </td>
                                    <td>
                                        <small><?= esc(substr($course['description'] ?? '', 0, 50)) ?>...</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info material-count-badge" data-course-id="<?= $course['id'] ?>" id="material-count-<?= $course['id'] ?>">
                                            <?= $courseMaterialCount ?> <?= $courseMaterialCount == 1 ? 'file' : 'files' ?>
                                        </span>
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
            <?php 
            // Debug: Check if recentMaterials exists and has data
            $hasRecentMaterials = isset($recentMaterials) && is_array($recentMaterials) && !empty($recentMaterials);
            ?>
            <?php if ($hasRecentMaterials): ?>
                <div class="list-group" id="recent-materials-list">
                    <?php foreach ($recentMaterials as $material): ?>
                        <?php
                            // Get course name for display
                            $courseModel = new \App\Models\CourseModel();
                            $materialCourse = $courseModel->find($material['course_id']);
                            $courseName = $materialCourse ? $materialCourse['title'] : 'Course ID: ' . $material['course_id'];
                        ?>
                        <div class="list-group-item" data-material-id="<?= $material['id'] ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="fas fa-file-pdf me-2 text-danger"></i>
                                        <strong><?= esc($material['file_name']) ?></strong>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-book me-1"></i><?= esc($courseName) ?> | 
                                        <i class="fas fa-clock me-1"></i><?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                    </small>
                                </div>
                                <div class="ms-3">
                                    <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="<?= base_url('materials/delete/' . $material['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this material?');" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info" id="no-materials-message">
                    <i class="fas fa-info-circle me-2"></i>
                    No materials uploaded yet. Upload your first material to see it here!
                    <?php if (session()->getFlashdata('success')): ?>
                        <br><small class="text-muted mt-2 d-block">
                            <i class="fas fa-spinner fa-spin me-1"></i>
                            Refreshing page to show uploaded material...
                        </small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Function to update Recently Uploaded Materials section dynamically
        // Make it globally available
        window.updateRecentMaterials = function(material) {
            const recentList = document.getElementById('recent-materials-list');
            const noMaterialsMsg = document.getElementById('no-materials-message');
            
            // If no materials list exists, create it
            if (!recentList && noMaterialsMsg) {
                // Remove "no materials" message
                noMaterialsMsg.remove();
                
                // Create new list group
                const newList = document.createElement('div');
                newList.className = 'list-group';
                newList.id = 'recent-materials-list';
                
                // Get the card body
                const cardBody = document.querySelector('.card.shadow-sm .card-body');
                if (cardBody) {
                    cardBody.appendChild(newList);
                }
            }
            
            // Get the list (create if needed)
            let list = document.getElementById('recent-materials-list');
            if (!list) {
                const cardBody = document.querySelector('.card.shadow-sm .card-body');
                if (cardBody) {
                    list = document.createElement('div');
                    list.className = 'list-group';
                    list.id = 'recent-materials-list';
                    cardBody.appendChild(list);
                } else {
                    return; // Can't find container
                }
            }
            
            // Format date
            const uploadDate = new Date(material.created_at);
            const formattedDate = uploadDate.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Create new material item
            const newItem = document.createElement('div');
            newItem.className = 'list-group-item';
            newItem.setAttribute('data-material-id', material.id);
            newItem.style.animation = 'slideIn 0.5s ease-out';
            newItem.style.backgroundColor = '#d4edda';
            
            newItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>
                            <strong>${escapeHtml(material.file_name)}</strong>
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-book me-1"></i>${escapeHtml(material.course_name)} | 
                            <i class="fas fa-clock me-1"></i>${formattedDate}
                        </small>
                    </div>
                    <div class="ms-3">
                        <a href="<?= base_url('materials/download/') ?>${material.id}" class="btn btn-sm btn-outline-primary" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="<?= base_url('materials/delete/') ?>${material.id}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this material?');" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            `;
            
            // Insert at the beginning of the list
            list.insertBefore(newItem, list.firstChild);
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                newItem.style.backgroundColor = '';
            }, 3000);
            
            // Update total materials count
            const totalMaterialsStat = document.getElementById('total-materials-stat');
            if (totalMaterialsStat) {
                const currentCount = parseInt(totalMaterialsStat.textContent) || 0;
                totalMaterialsStat.textContent = currentCount + 1;
            }
            
            // Update course material count if visible
            const courseCountBadge = document.getElementById('material-count-' + material.course_id);
            if (courseCountBadge) {
                const currentCount = parseInt(courseCountBadge.textContent) || 0;
                courseCountBadge.textContent = (currentCount + 1) + ' ' + (currentCount === 0 ? 'file' : 'files');
            }
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Add slide-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        `;
        document.head.appendChild(style);
        
        // Debug on page load
        document.addEventListener('DOMContentLoaded', function() {
            const recentList = document.getElementById('recent-materials-list');
            const noMaterialsMsg = document.getElementById('no-materials-message');
            
            console.log('Recent materials list exists:', !!recentList);
            console.log('No materials message exists:', !!noMaterialsMsg);
        });
    </script>

<?= $this->endSection() ?>
