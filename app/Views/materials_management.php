<?php
$this->extend('template');
$this->section('content');
?>

<div class="materials-page container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="fas fa-folder-open"></i> Materials Management</h2>
            <p class="text-muted mb-0">Upload and manage course materials (PDF, DOCX).</p>
        </div>
        <div>
            <button id="refreshMaterials" class="btn btn-outline-secondary">Refresh</button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="materialsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Course Title</th>
                            <th class="d-none d-md-table-cell">Description</th>
                            <th class="text-center">Materials Count</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample rows inserted by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <template id="materials-row-template">
        <tr>
            <td class="course-title"></td>
            <td class="course-desc d-none d-md-table-cell text-muted small"></td>
            <td class="text-center materials-count">0</td>
            <td class="text-end">
                <div class="d-flex justify-content-end gap-2 align-items-center">
                    <label class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer;">
                        <i class="fas fa-upload"></i> Upload
                        <input type="file" class="d-none upload-input" accept=".pdf,application/pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </label>
                    <button class="btn btn-sm btn-outline-secondary view-list-btn">View Files</button>
                </div>
            </td>
        </tr>
    </template>

    <div id="materialsModal" class="modal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Uploaded Files</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul id="filesList" class="list-group"></ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

</div>

<style>
    .materials-page h2 { font-weight: 700; }
    #materialsTable tbody tr td { vertical-align: middle; }
    .upload-input { display: none; }
    .table-hover tbody tr:hover { background: #fbfdff; }
    .badge-materials { min-width: 36px; display: inline-block; }

    @media (max-width: 576px) {
        .materials-page { padding: 0 0.5rem; }
    }
</style>

<script>
    (function() {
        // Sample courses
        const sampleCourses = [
            { id: 1, title: 'Introduction to PHP', description: 'Basics of PHP programming and server-side scripting.', materials: [] },
            { id: 2, title: 'Advanced JavaScript', description: 'Deep dive into modern JavaScript patterns and tooling.', materials: [] },
            { id: 3, title: 'Database Design', description: 'ER modeling, normalization, and SQL best practices.', materials: [] },
            { id: 4, title: 'Web Development Basics', description: 'HTML, CSS, and foundational web concepts.', materials: [] },
            { id: 5, title: 'Python for Beginners', description: 'Introduction to Python programming for novices.', materials: [] }
        ];

        const tableBody = document.querySelector('#materialsTable tbody');
        const rowTemplate = document.getElementById('materials-row-template');

        function renderTable() {
            tableBody.innerHTML = '';
            sampleCourses.forEach(course => {
                const tpl = rowTemplate.content.cloneNode(true);
                tpl.querySelector('.course-title').textContent = course.title;
                tpl.querySelector('.course-desc').textContent = course.description;
                tpl.querySelector('.materials-count').textContent = course.materials.length;

                const uploadInput = tpl.querySelector('.upload-input');
                uploadInput.dataset.courseId = course.id;

                // Bind file change
                uploadInput.addEventListener('change', function(e) {
                    const file = this.files[0];
                    if (!file) return;
                    const courseId = parseInt(this.dataset.courseId, 10);
                    // Call upload handler (simulated or real)
                    uploadCourseMaterial(courseId, file).then(() => {
                        // update local state
                        const c = sampleCourses.find(x => x.id === courseId);
                        if (c) c.materials.push({ name: file.name, size: file.size, type: file.type, uploadedAt: new Date().toISOString() });
                        renderTable();
                    }).catch(err => {
                        alert('Upload failed: ' + err.message);
                    });
                });

                // View list button
                tpl.querySelector('.view-list-btn').addEventListener('click', function() {
                    const courseId = course.id;
                    const courseData = sampleCourses.find(x => x.id === courseId);
                    const filesList = document.getElementById('filesList');
                    filesList.innerHTML = '';
                    if (courseData && courseData.materials.length > 0) {
                        courseData.materials.forEach(m => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center';
                            li.innerHTML = `<div><strong>${m.name}</strong><div class="small text-muted">${Math.round(m.size/1024)} KB • ${m.type}</div></div>`;
                            filesList.appendChild(li);
                        });
                    } else {
                        filesList.innerHTML = '<li class="list-group-item text-muted">No files uploaded for this course.</li>';
                    }
                    const modalEl = document.getElementById('materialsModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });

                tableBody.appendChild(tpl);
            });

            // Attach upload input triggers (label click opens file dialog)
            document.querySelectorAll('.upload-input').forEach(inp => inp.style.display = 'none');
        }

        // Simulate upload function - replace this with real XHR/fetch to server endpoint
        function uploadCourseMaterial(courseId, file) {
            return new Promise((resolve, reject) => {
                // Validate file types
                const allowed = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowed.includes(file.type) && !file.name.match(/\.(pdf|doc|docx)$/i)) {
                    return reject(new Error('Invalid file type. Please upload PDF or Word documents.'));
                }

                // Simulate network latency
                setTimeout(() => {
                    console.log('Uploaded', file.name, 'for course', courseId);
                    resolve({ success: true });
                }, 700);
            });
        }

        // Expose uploadCourseMaterial globally as requested
        window.uploadCourseMaterial = uploadCourseMaterial;

        // Initial render
        renderTable();

        // Refresh button
        document.getElementById('refreshMaterials').addEventListener('click', function() {
            renderTable();
        });

    })();
</script>

<?php $this->endSection(); ?>