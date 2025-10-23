<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Upload Material for Course <?php echo $course_id; ?></h2>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?php echo session()->getFlashdata('success'); ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?php echo session()->getFlashdata('error'); ?></div>
        <?php endif; ?>
        <form action="<?php echo base_url('materials/upload/' . $course_id); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="material_file" class="form-label">Select File</label>
                <input type="file" class="form-control" id="material_file" name="material_file" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        <a href="<?php echo base_url('teacher/dashboard'); ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
