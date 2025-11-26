<?php
$this->extend('template');
$this->section('content');
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>
                <i class="fas fa-search"></i> Search Results
                <?php if (!empty($searchTerm)): ?>
                    <small class="text-muted">for "<?= htmlspecialchars($searchTerm) ?>"</small>
                <?php endif; ?>
            </h2>
            <hr>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-12">
            <form method="GET" action="<?= site_url('course/search') ?>" class="input-group input-group-lg">
                <input type="text" class="form-control" name="search" placeholder="Search courses by title or description..." 
                       value="<?= htmlspecialchars($searchTerm) ?>" required>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </form>
        </div>
    </div>

    <!-- Results Summary -->
    <?php if (!empty($searchTerm)): ?>
    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle"></i> Found <strong><?= count($courses) ?></strong> course<?= count($courses) !== 1 ? 's' : '' ?>
    </div>
    <?php endif; ?>

    <!-- Courses Display -->
    <?php if (!empty($courses)): ?>
    <div class="row">
        <?php foreach ($courses as $course): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <h5 class="card-title text-primary">
                        <i class="fas fa-book"></i> <?= htmlspecialchars($course['title']) ?>
                    </h5>
                    <p class="card-text text-muted small">
                        <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>
                        <?php if (strlen($course['description']) > 100): ?>
                            ...
                        <?php endif; ?>
                    </p>
                    
                    <div class="mt-3">
                        <small class="text-secondary">
                            <i class="fas fa-calendar"></i> 
                            Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="<?= site_url('dashboard') ?>" class="btn btn-sm btn-outline-primary enroll-btn" data-course-id="<?= $course['id'] ?>">
                        <i class="fas fa-plus-circle"></i> Enroll
                    </a>
                    <a href="<?= site_url('course/details/' . $course['id']) ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-warning" role="alert">
        <i class="fas fa-search"></i>
        <strong>No courses found</strong>
        <?php if (!empty($searchTerm)): ?>
            for "<?= htmlspecialchars($searchTerm) ?>". Try a different search term.
        <?php else: ?>
            Try searching for a course.
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <a href="<?= site_url('dashboard') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- CSS for hover effect -->
<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease-in-out;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .enroll-btn {
        cursor: pointer;
    }

    .enroll-btn:hover {
        background-color: #0d6efd;
        color: white;
    }
</style>

<!-- Enroll Button Handler -->
<script>
$(document).ready(function() {
    $('.enroll-btn').on('click', function(e) {
        e.preventDefault();
        var courseId = $(this).data('course-id');
        var button = $(this);
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enrolling...');
        
        $.post('<?= site_url('/course/enroll') ?>', {
            course_id: courseId
        }, function(response) {
            if (response.success) {
                button.html('<i class="fas fa-check-circle"></i> Enrolled').removeClass('btn-outline-primary').addClass('btn-success').prop('disabled', true);
                setTimeout(function() {
                    window.location.href = '<?= site_url('/dashboard') ?>';
                }, 1500);
            } else {
                alert('Error: ' + response.message);
                button.prop('disabled', false).html('<i class="fas fa-plus-circle"></i> Enroll');
            }
        }, 'json').fail(function() {
            alert('An error occurred. Please try again.');
            button.prop('disabled', false).html('<i class="fas fa-plus-circle"></i> Enroll');
        });
    });
});
</script>

<?php $this->endSection(); ?>
