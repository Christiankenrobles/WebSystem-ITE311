<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-edit text-primary"></i> Edit Schedule
        </h1>
        <div>
            <a href="<?= base_url('schedule') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Schedule
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i><?= esc($course['title']) ?>
                    </h5>
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

                    <form id="scheduleForm" method="POST" action="<?= base_url('course/update-schedule/' . $course['id']) ?>">
                        <?= csrf_field() ?>

                        <!-- Schedule Days -->
                        <div class="mb-3">
                            <label for="schedule_days" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Days of Week
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="schedule_days" 
                                   name="schedule_days" 
                                   value="<?= esc($course['schedule_days'] ?? '') ?>"
                                   placeholder="e.g., Monday,Wednesday,Friday or MWF">
                            <small class="form-text text-muted">
                                Enter days separated by commas. Examples: "Monday,Wednesday,Friday", "MWF", "Mon,Wed,Fri"
                            </small>
                        </div>

                        <!-- Schedule Time -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="schedule_time_start" class="form-label">
                                    <i class="fas fa-clock me-1"></i>Start Time
                                </label>
                                <input type="time" 
                                       class="form-control" 
                                       id="schedule_time_start" 
                                       name="schedule_time_start" 
                                       value="<?= esc($course['schedule_time_start'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_time_end" class="form-label">
                                    <i class="fas fa-clock me-1"></i>End Time
                                </label>
                                <input type="time" 
                                       class="form-control" 
                                       id="schedule_time_end" 
                                       name="schedule_time_end" 
                                       value="<?= esc($course['schedule_time_end'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Schedule Location -->
                        <div class="mb-3">
                            <label for="schedule_location" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Location/Room
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="schedule_location" 
                                   name="schedule_location" 
                                   value="<?= esc($course['schedule_location'] ?? '') ?>"
                                   placeholder="e.g., Room 101, Online, Building A - Room 205">
                        </div>

                        <!-- Schedule Type -->
                        <div class="mb-3">
                            <label for="schedule_type" class="form-label">
                                <i class="fas fa-laptop me-1"></i>Schedule Type
                            </label>
                            <select class="form-select" id="schedule_type" name="schedule_type">
                                <option value="regular" <?= ($course['schedule_type'] ?? 'regular') === 'regular' ? 'selected' : '' ?>>
                                    Regular (Face-to-Face)
                                </option>
                                <option value="online" <?= ($course['schedule_type'] ?? '') === 'online' ? 'selected' : '' ?>>
                                    Online
                                </option>
                                <option value="hybrid" <?= ($course['schedule_type'] ?? '') === 'hybrid' ? 'selected' : '' ?>>
                                    Hybrid
                                </option>
                            </select>
                        </div>

                        <!-- Course Info (Read-only) -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Course Information</h6>
                            <p class="mb-1"><strong>Title:</strong> <?= esc($course['title']) ?></p>
                            <?php if (!empty($course['description'])): ?>
                                <p class="mb-0"><strong>Description:</strong> <?= esc($course['description']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('schedule') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Schedule Preview -->
            <?php if (!empty($course['schedule_days']) || !empty($course['schedule_time_start'])): ?>
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Current Schedule Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Days:</strong> 
                                    <span class="badge bg-primary"><?= esc($course['schedule_days'] ?? 'Not set') ?></span>
                                </p>
                                <p class="mb-1">
                                    <strong>Time:</strong> 
                                    <?php if (!empty($course['schedule_time_start'])): ?>
                                        <span class="badge bg-success">
                                            <?= date('g:i A', strtotime($course['schedule_time_start'])) ?>
                                            <?php if (!empty($course['schedule_time_end'])): ?>
                                                - <?= date('g:i A', strtotime($course['schedule_time_end'])) ?>
                                            <?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Location:</strong> 
                                    <?= !empty($course['schedule_location']) ? esc($course['schedule_location']) : '<span class="text-muted">Not set</span>' ?>
                                </p>
                                <p class="mb-0">
                                    <strong>Type:</strong> 
                                    <span class="badge bg-info"><?= ucfirst($course['schedule_type'] ?? 'regular') ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Form validation
    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
        const startTime = document.getElementById('schedule_time_start').value;
        const endTime = document.getElementById('schedule_time_end').value;

        if (startTime && endTime && startTime >= endTime) {
            e.preventDefault();
            alert('End time must be after start time!');
            return false;
        }
    });

    // Auto-format days input
    document.getElementById('schedule_days').addEventListener('blur', function() {
        let value = this.value.trim();
        if (value) {
            // Convert common abbreviations to full names
            const dayMap = {
                'M': 'Monday',
                'T': 'Tuesday',
                'W': 'Wednesday',
                'TH': 'Thursday',
                'F': 'Friday',
                'S': 'Saturday',
                'SU': 'Sunday',
                'Mon': 'Monday',
                'Tue': 'Tuesday',
                'Wed': 'Wednesday',
                'Thu': 'Thursday',
                'Fri': 'Friday',
                'Sat': 'Saturday',
                'Sun': 'Sunday'
            };

            const days = value.split(',').map(d => {
                d = d.trim();
                return dayMap[d] || d;
            });

            this.value = days.join(',');
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
    .form-label {
        font-weight: 600;
        color: #495057;
    }
</style>

<?= $this->endSection() ?>

