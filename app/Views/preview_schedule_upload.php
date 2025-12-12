<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-eye text-primary"></i> Preview Schedule Upload
        </h1>
        <div>
            <a href="<?= base_url('course/upload-schedule') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Upload
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-warning">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Warnings</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>Schedule Data Preview
                <small class="ms-2">(<?= count($previewData) ?> rows)</small>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Row</th>
                            <th>Course Title</th>
                            <th>Matched Course</th>
                            <th>Days</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($previewData as $row): ?>
                            <tr class="<?= $row['matched_course'] ? '' : 'table-warning' ?>">
                                <td><?= $row['row_number'] ?></td>
                                <td><strong><?= esc($row['course_title']) ?></strong></td>
                                <td>
                                    <?php if ($row['matched_course']): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i><?= esc($row['matched_course']['title']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Not Found
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['schedule_days'])): ?>
                                        <span class="badge bg-primary"><?= esc($row['schedule_days']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['schedule_time_start'])): ?>
                                        <?= esc($row['schedule_time_start']) ?>
                                        <?php if (!empty($row['schedule_time_end'])): ?>
                                            - <?= esc($row['schedule_time_end']) ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= !empty($row['schedule_location']) ? esc($row['schedule_location']) : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($row['schedule_type']) ?></span>
                                </td>
                                <td>
                                    <?php if ($row['matched_course']): ?>
                                        <span class="badge bg-success">Ready</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Error</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-info-circle me-2"></i>Import Summary</h6>
                    <ul class="list-unstyled">
                        <li><strong>Total Rows:</strong> <?= count($previewData) ?></li>
                        <li><strong>Ready to Import:</strong> <span class="text-success"><?= count(array_filter($previewData, function($r) { return $r['matched_course']; })) ?></span></li>
                        <li><strong>Errors:</strong> <span class="text-danger"><?= count(array_filter($previewData, function($r) { return !$r['matched_course']; })) ?></span></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                    <ul class="small">
                        <li>Only rows with matched courses will be imported</li>
                        <li>Existing schedule data will be overwritten</li>
                        <li>Make sure course titles in the file match exactly with the database</li>
                        <li>You can cancel and fix the file if needed</li>
                    </ul>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="<?= base_url('course/upload-schedule') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="button" id="importBtn" class="btn btn-primary" 
                        <?= count(array_filter($previewData, function($r) { return $r['matched_course']; })) === 0 ? 'disabled' : '' ?>>
                    <i class="fas fa-download me-2"></i>Import Schedules
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('importBtn').addEventListener('click', function() {
        if (!confirm('Are you sure you want to import these schedules? This will update the existing schedule data for matched courses.')) {
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importing...';

        fetch('<?= base_url('course/import-schedule') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = data.message;
                if (data.errors && data.errors.length > 0) {
                    message += '\n\nErrors:\n' + data.errors.slice(0, 5).join('\n');
                    if (data.errors.length > 5) {
                        message += '\n... and ' + (data.errors.length - 5) + ' more errors';
                    }
                }
                alert(message);
                window.location.href = '<?= base_url('admin/schedule') ?>';
            } else {
                alert('Import failed: ' + (data.message || 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-download me-2"></i>Import Schedules';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during import. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-download me-2"></i>Import Schedules';
        });
    });
</script>

<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .table-warning {
        background-color: #fff3cd;
    }
</style>

<?= $this->endSection() ?>

