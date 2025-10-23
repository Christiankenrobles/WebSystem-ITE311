<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Materials for Course: <?php echo esc($course['title']); ?></h2>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?php echo session()->getFlashdata('success'); ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?php echo session()->getFlashdata('error'); ?></div>
        <?php endif; ?>

        <?php if (!empty($materials)): ?>
            <div class="list-group">
                <?php foreach ($materials as $material): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?php echo esc($material['file_name']); ?></h5>
                            <small>Uploaded on: <?php echo esc($material['created_at']); ?></small>
                        </div>
                        <a href="<?php echo base_url('materials/download/' . $material['id']); ?>" class="btn btn-primary">Download</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No materials available for this course.</p>
        <?php endif; ?>

        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
