<?php
$this->extend('template');
$this->section('content');
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-book"></i> Course Catalog
            </h1>
            <p class="text-muted">Explore and discover courses available for enrollment</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Notifications Button (polls optional endpoint) -->
            <button id="notification-button" type="button" class="btn btn-outline-info position-relative" title="Notifications">
                <i class="fas fa-bell"></i>
                <span id="notification-count" class="badge bg-danger position-absolute" style="top:-6px;right:-6px;display:none;">0</span>
            </button>

            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('courses') ?>" id="courses-search-form" class="mb-0">
                <div class="row g-3 align-items-end">
                    <!-- Search Input (Client-side) -->
                    <div class="col-md-4">
                        <label for="client-search" class="form-label">
                            <i class="fas fa-search"></i> Quick Filter
                        </label>
                        <input type="text" class="form-control form-control-lg" id="client-search" 
                               placeholder="Filter by title or description..." 
                               autocomplete="off">
                        <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> Instant filtering</small>
                    </div>

                    <!-- Server Search Input -->
                    <div class="col-md-3">
                        <label for="course-search" class="form-label">
                            <i class="fas fa-database"></i> Search Database
                        </label>
                        <input type="text" class="form-control form-control-lg" id="course-search" name="q" 
                               placeholder="Search server..." 
                               value="<?= isset($searchTerm) ? htmlspecialchars($searchTerm) : '' ?>"
                               autocomplete="off">
                        <small class="text-muted d-block mt-1">Server-side search</small>
                    </div>

                    <!-- Sort Options -->
                    <div class="col-md-2">
                        <label for="sort-filter" class="form-label">
                            <i class="fas fa-sort"></i> Sort By
                        </label>
                        <select class="form-select form-select-lg" id="sort-filter">
                            <option value="title-asc">Title (A-Z)</option>
                            <option value="title-desc">Title (Z-A)</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>

                    <!-- Filter by Instructor (Client-side) -->
                    <div class="col-md-2">
                        <label for="instructor-filter" class="form-label">
                            <i class="fas fa-user-tie"></i> Instructor
                        </label>
                        <select class="form-select form-select-lg" id="instructor-filter">
                            <option value="">All Instructors</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-lg w-100" type="submit" title="Server search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Status -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div id="filter-status" class="small text-muted"></div>
                    </div>
                </div>

                <!-- Quick Filter Tags -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div id="filter-tags" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Autocomplete Suggestions -->
    <div id="course-suggestions" class="mb-4"></div>

    <!-- Results Summary with Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <?php if (!empty($searchTerm) || !empty($courses)): ?>
                    <div class="alert alert-info mb-0" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>Results:</strong> Showing <?= count($courses) ?> course<?= count($courses) !== 1 ? 's' : '' ?>
                        <?php if (!empty($searchTerm)): ?>
                            matching "<?= htmlspecialchars($searchTerm) ?>"
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-muted">
                        <i class="fas fa-list"></i> <span id="course-count">Loading courses...</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="btn-group" role="group" aria-label="View options">
                    <input type="radio" class="btn-check" name="view-options" id="grid-view" checked>
                    <label class="btn btn-outline-secondary btn-sm" for="grid-view" title="Grid view">
                        <i class="fas fa-th"></i> Grid
                    </label>
                    
                    <input type="radio" class="btn-check" name="view-options" id="list-view">
                    <label class="btn btn-outline-secondary btn-sm" for="list-view" title="List view">
                        <i class="fas fa-list"></i> List
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Display Grid View -->
    <?php if (!empty($courses) && count($courses) > 0): ?>
        <div id="courses-container" class="row courses-grid">
            <?php foreach ($courses as $course): ?>
            <div class="col-lg-4 col-md-6 mb-4 course-item course-item-grid" 
                 data-course-id="<?= $course['id'] ?>"
                 data-title="<?= htmlspecialchars($course['title']) ?>"
                 data-description="<?= htmlspecialchars($course['description']) ?>"
                 data-instructor="<?= htmlspecialchars($course['instructor_id'] ?? 'N/A') ?>"
                 data-date="<?= $course['created_at'] ?>"
                 data-search-text="<?= htmlspecialchars(strtolower($course['title'] . ' ' . $course['description'])) ?>">
                <div class="card h-100 shadow-sm hover-shadow-lg border-0 course-card">
                    <!-- Course Header -->
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-book-open"></i> <span class="course-title"><?= htmlspecialchars($course['title']) ?></span>
                        </h5>
                    </div>

                    <!-- Course Body -->
                    <div class="card-body flex-grow-1">
                        <p class="card-text text-muted course-description">
                            <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>
                            <?php if (strlen($course['description']) > 100): ?>
                                <span class="text-secondary">...</span>
                            <?php endif; ?>
                        </p>

                        <!-- Course Meta -->
                        <div class="course-meta small text-muted mt-3">
                            <div class="mb-2">
                                <i class="fas fa-calendar text-info"></i>
                                <strong>Created:</strong> <span class="course-date"><?= date('M d, Y', strtotime($course['created_at'])) ?></span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user text-success"></i>
                                <strong>Instructor:</strong> <span class="course-instructor"><?= htmlspecialchars($course['instructor_id'] ?? 'N/A') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Footer -->
                    <div class="card-footer bg-white border-top">
                        <div class="d-grid gap-2">
                            <a href="<?= site_url('dashboard') ?>" class="btn btn-primary btn-sm enroll-course-btn" data-course-id="<?= $course['id'] ?>">
                                <i class="fas fa-plus-circle"></i> Enroll Now
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Courses Display List View -->
        <div id="courses-list-view" class="courses-list d-none">
            <div class="row">
                <div class="col-12">
                    <?php foreach ($courses as $course): ?>
                    <div class="course-item-list mb-3 p-3 border rounded bg-white course-item" 
                         data-course-id="<?= $course['id'] ?>"
                         data-title="<?= htmlspecialchars($course['title']) ?>"
                         data-description="<?= htmlspecialchars($course['description']) ?>"
                         data-instructor="<?= htmlspecialchars($course['instructor_id'] ?? 'N/A') ?>"
                         data-date="<?= $course['created_at'] ?>"
                         data-search-text="<?= htmlspecialchars(strtolower($course['title'] . ' ' . $course['description'])) ?>">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">
                                    <i class="fas fa-book-open text-primary"></i>
                                    <span class="course-title"><?= htmlspecialchars($course['title']) ?></span>
                                </h6>
                                <p class="text-muted small mb-2 course-description">
                                    <?= htmlspecialchars(substr($course['description'], 0, 120)) ?>
                                    <?php if (strlen($course['description']) > 120): ?>
                                        <span>...</span>
                                    <?php endif; ?>
                                </p>
                                <div class="small text-muted">
                                    <span class="me-3">
                                        <i class="fas fa-user"></i>
                                        <span class="course-instructor"><?= htmlspecialchars($course['instructor_id'] ?? 'N/A') ?></span>
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar"></i>
                                        <span class="course-date"><?= date('M d, Y', strtotime($course['created_at'])) ?></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-primary btn-sm enroll-course-btn me-2" data-course-id="<?= $course['id'] ?>">
                                    <i class="fas fa-plus-circle"></i> Enroll
                                </button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-eye"></i> Details
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Pagination Placeholder (for Step 7) -->
        <nav aria-label="Page navigation" id="pagination-container" class="mt-4 d-none">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>

    <?php else: ?>
        <!-- No Results State -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5 no-results-container">
                    <div class="mb-4">
                        <i class="fas fa-search" style="font-size: 64px; color: #dee2e6;"></i>
                    </div>
                    <h3 class="text-muted mb-2">No Courses Found</h3>
                    <p class="text-muted mb-4">
                        <?php if (!empty($searchTerm)): ?>
                            We couldn't find any courses matching "<strong><?= htmlspecialchars($searchTerm) ?></strong>".
                            <br><small>Try a different search term or browse all courses.</small>
                        <?php else: ?>
                            No courses are currently available.
                            <br><small>Check back later or contact an administrator.</small>
                        <?php endif; ?>
                    </p>
                    <div class="btn-group" role="group">
                        <a href="<?= site_url('courses') ?>" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Clear Search
                        </a>
                        <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- CSS Styles -->
<style>
    /* View Switching */
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .courses-grid.hide {
        display: none;
    }

    .courses-list {
        display: block;
    }

    .courses-list.d-none {
        display: none;
    }

    .course-item-list {
        transition: all 0.3s ease-in-out;
        border-left: 4px solid #0d6efd;
    }

    .course-item-list:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
    }

    /* Course Cards */
    .hover-shadow-lg {
        transition: all 0.3s ease-in-out;
    }

    .hover-shadow-lg:hover {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        transform: translateY(-2px);
    }

    .course-card {
        border-radius: 8px;
        overflow: hidden;
    }

    .course-card:hover .card-title {
        color: #0056b3;
    }

    .course-meta {
        border-top: 1px solid #e9ecef;
        padding-top: 1rem;
    }

    /* Course Container Animation */
    .course-item {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Suggestions Dropdown */
    #course-suggestions {
        position: relative;
    }

    .suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
    }

    .suggestion-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }

    .suggestion-item:hover {
        background-color: #f8f9fa;
    }

    .suggestion-item:last-child {
        border-bottom: none;
    }

    mark {
        background-color: #fff3cd;
        padding: 2px 4px;
        border-radius: 3px;
    }

    .form-control-lg:focus,
    .form-select-lg:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Filter Tags Styling */
    #filter-tags {
        margin-top: 1rem;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge .btn-close {
        margin-left: 0.25rem;
        opacity: 0.7;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .badge .btn-close:hover {
        opacity: 1;
    }

    /* Filter Status */
    #filter-status {
        padding: 0.5rem 0;
        min-height: 1.5rem;
    }

    /* No Results Container */
    .no-results-container {
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    /* Results Summary */
    .alert-info {
        background-color: #cfe2ff;
        border-color: #b6d4fe;
        color: #084298;
    }

    /* View Options Buttons */
    .btn-check:checked + .btn-outline-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }

        .form-control-lg,
        .form-select-lg,
        .btn-lg {
            font-size: 0.95rem;
            padding: 0.4rem 0.8rem;
        }

        #filter-tags {
            flex-direction: column;
        }

        .badge {
            width: 100%;
            justify-content: space-between;
        }

        .course-item-list .row {
            flex-direction: column;
        }

        .course-item-list .col-md-4 {
            margin-top: 1rem;
        }

        .course-item-list .text-end {
            text-align: left !important;
        }
    }

    /* Pagination Styling */
    #pagination-container {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #dee2e6;
    }

    .pagination {
        gap: 0.25rem;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .page-link {
        color: #0d6efd;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
    }

    .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
    }

    /* Search Shortcut Hint */
    .search-shortcut-hint {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.75rem;
        background-color: #e9ecef;
        padding: 0.25rem 0.5rem;
        border-radius: 3px;
        color: #666;
        pointer-events: none;
    }
</style>

<!-- JavaScript for Search, Filtering, and Enrollment -->
<script>
$(document).ready(function() {
    // ==========================================
    // CLIENT-SIDE FILTERING
    // ==========================================
    
    // Store original courses data
    var originalCourses = [];
    var filteredCourses = [];
    
    // Initialize: Extract course data from DOM
    function initializeCoursesData() {
        originalCourses = [];
        $('.course-item').each(function() {
            originalCourses.push({
                id: $(this).data('course-id'),
                title: $(this).data('title'),
                description: $(this).data('description'),
                instructor: $(this).data('instructor'),
                date: $(this).data('date'),
                searchText: $(this).data('search-text'),
                element: $(this)
            });
        });
        
        filteredCourses = originalCourses.slice();
        populateFilters();
    }
    
    // Populate filter dropdowns with unique values
    function populateFilters() {
        // Get unique instructors
        var instructors = [...new Set(originalCourses.map(c => c.instructor))];
        var instructorSelect = $('#instructor-filter');
        
        instructors.forEach(function(instructor) {
            if (instructor && !instructorSelect.find('option[value="' + instructor + '"]').length) {
                instructorSelect.append('<option value="' + instructor + '">' + escapeHtml(instructor) + '</option>');
            }
        });
    }
    
    // Apply all filters and sorting
    function applyFilters() {
        var searchText = $('#client-search').val().toLowerCase().trim();
        var selectedInstructor = $('#instructor-filter').val();
        var sortOption = $('#sort-filter').val();
        
        // Filter courses
        filteredCourses = originalCourses.filter(function(course) {
            var matchesSearch = searchText === '' || course.searchText.includes(searchText);
            var matchesInstructor = selectedInstructor === '' || course.instructor === selectedInstructor;
            return matchesSearch && matchesInstructor;
        });
        
        // Sort courses
        filteredCourses.sort(function(a, b) {
            switch(sortOption) {
                case 'title-asc':
                    return a.title.localeCompare(b.title);
                case 'title-desc':
                    return b.title.localeCompare(a.title);
                case 'newest':
                    return new Date(b.date) - new Date(a.date);
                case 'oldest':
                    return new Date(a.date) - new Date(b.date);
                default:
                    return 0;
            }
        });
        
        // Update display
        updateCourseDisplay();
        updateFilterStatus();
        updateFilterTags();
    }
    
    // Update course display based on filtered results
    function updateCourseDisplay() {
        var container = $('#courses-container');
        var listView = $('#courses-list-view');
        
        if (filteredCourses.length === 0) {
            container.html(
                '<div class="col-12">' +
                '<div class="text-center py-5">' +
                '<i class="fas fa-search" style="font-size: 48px; color: #ccc;"></i>' +
                '<h4 class="text-muted mt-3">No Courses Match Your Filters</h4>' +
                '<p class="text-muted">Try adjusting your search or filters</p>' +
                '</div>' +
                '</div>'
            );
            return;
        }
        
        // Update both grid and list views
        // Rearrange grid view elements
        filteredCourses.forEach(function(course) {
            container.append(course.element);
        });
        
        // Rearrange list view elements
        var listContainer = listView.find('.row');
        filteredCourses.forEach(function(course) {
            var listElement = course.element.find('.course-item-list');
            if (listElement.length > 0) {
                listContainer.append(listElement);
            }
        });
    }
    
    // Update filter status message
    function updateFilterStatus() {
        var status = $('#filter-status');
        var searchText = $('#client-search').val().trim();
        var selectedInstructor = $('#instructor-filter').val();
        
        var statusText = '';
        
        if (searchText || selectedInstructor) {
            statusText = '<i class="fas fa-filter"></i> <strong>Active Filters:</strong> ';
            
            var filters = [];
            if (searchText) {
                filters.push('Search: <strong>"' + escapeHtml(searchText) + '"</strong>');
            }
            if (selectedInstructor) {
                filters.push('Instructor: <strong>' + escapeHtml(selectedInstructor) + '</strong>');
            }
            
            statusText += filters.join(' | ');
            statusText += ' <span class="text-muted">(' + filteredCourses.length + ' result' + (filteredCourses.length !== 1 ? 's' : '') + ')</span>';
        } else {
            statusText = '<i class="fas fa-th"></i> <strong>Showing all courses</strong> (' + filteredCourses.length + ' total)';
        }
        
        status.html(statusText);
    }
    
    // Update filter tags (quick filters)
    function updateFilterTags() {
        var tagsContainer = $('#filter-tags');
        tagsContainer.empty();
        
        var searchText = $('#client-search').val().trim();
        var selectedInstructor = $('#instructor-filter').val();
        
        if (searchText) {
            var searchTag = $('<span class="badge bg-primary">' +
                '<i class="fas fa-search"></i> ' + escapeHtml(searchText) +
                ' <button type="button" class="btn-close btn-close-white ms-2 clear-search" aria-label="Clear"></button>' +
                '</span>');
            tagsContainer.append(searchTag);
        }
        
        if (selectedInstructor) {
            var instructorTag = $('<span class="badge bg-info">' +
                '<i class="fas fa-user-tie"></i> ' + escapeHtml(selectedInstructor) +
                ' <button type="button" class="btn-close btn-close-white ms-2 clear-instructor" aria-label="Clear"></button>' +
                '</span>');
            tagsContainer.append(instructorTag);
        }
        
        // Clear filter handlers
        tagsContainer.on('click', '.clear-search', function() {
            $('#client-search').val('').trigger('input');
        });
        
        tagsContainer.on('click', '.clear-instructor', function() {
            $('#instructor-filter').val('').trigger('change');
        });
    }
    
    // Reset all filters
    function resetFilters() {
        $('#client-search').val('');
        $('#instructor-filter').val('');
        $('#sort-filter').val('title-asc');
        applyFilters();
    }
    
    // Event listeners for filters
    $('#client-search').on('input', function() {
        applyFilters();
    });
    
    $('#instructor-filter').on('change', function() {
        applyFilters();
    });
    
    $('#sort-filter').on('change', function() {
        applyFilters();
    });
    
    // ==========================================
    // VIEW SWITCHING (GRID / LIST)
    // ==========================================
    
    // Handle grid/list view switching
    $('#grid-view').on('change', function() {
        $('.courses-grid').removeClass('hide').addClass('d-block');
        $('#courses-list-view').addClass('d-none');
        // Store preference in localStorage
        localStorage.setItem('courseViewPreference', 'grid');
    });
    
    $('#list-view').on('change', function() {
        $('.courses-grid').addClass('hide').removeClass('d-block');
        $('#courses-list-view').removeClass('d-none');
        // Store preference in localStorage
        localStorage.setItem('courseViewPreference', 'list');
    });
    
    // Restore view preference from localStorage
    function restoreViewPreference() {
        var preference = localStorage.getItem('courseViewPreference') || 'grid';
        if (preference === 'list') {
            $('#list-view').prop('checked', true).trigger('change');
        } else {
            $('#grid-view').prop('checked', true);
            $('.courses-grid').removeClass('hide').addClass('d-block');
            $('#courses-list-view').addClass('d-none');
        }
    }
    
    // ==========================================
    
    var searchTimeout;
    
    $('#course-search').on('keyup', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val().trim();
        
        if (query.length < 2) {
            $('#course-suggestions').html('');
            return;
        }
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                type: 'GET',
                url: '<?= site_url('course/search') ?>',
                data: {q: query},
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.results && response.results.length > 0) {
                        var suggestionHtml = '<div class="suggestions-dropdown">';
                        
                        var limitedResults = response.results.slice(0, 8);
                        
                        limitedResults.forEach(function(course) {
                            suggestionHtml += '<div class="suggestion-item" data-course-id="' + course.id + '">' +
                                '<strong>' + escapeHtml(course.title) + '</strong>' +
                                '<br><small class="text-muted">' + escapeHtml(course.description.substring(0, 60)) + '...</small>' +
                                '</div>';
                        });
                        
                        suggestionHtml += '</div>';
                        $('#course-suggestions').html(suggestionHtml);
                    }
                }
            });
        }, 400);
    });

    // Handle suggestion selection
    $(document).on('click', '.suggestion-item', function() {
        var courseTitle = $(this).find('strong').text();
        $('#course-search').val(courseTitle);
        $('#course-suggestions').html('');
        $('#courses-search-form').submit();
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#course-search, #course-suggestions').length) {
            $('#course-suggestions').html('');
        }
    });

    // ==========================================
    // ENROLLMENT HANDLER
    // ==========================================
    
    $(document).on('click', '.enroll-course-btn', function(e) {
        e.preventDefault();
        var courseId = $(this).data('course-id');
        var $button = $(this);
        
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enrolling...');
        
        $.post('<?= site_url('course/enroll') ?>', {
            course_id: courseId
        }, function(response) {
            if (response.success) {
                $button.html('<i class="fas fa-check-circle"></i> Enrolled').removeClass('btn-primary').addClass('btn-success').prop('disabled', true);
                setTimeout(function() {
                    window.location.href = '<?= site_url('dashboard') ?>';
                }, 1500);
            } else {
                alert('Error: ' + response.message);
                $button.prop('disabled', false).html('<i class="fas fa-plus-circle"></i> Enroll Now');
            }
        }, 'json').fail(function() {
            alert('An error occurred. Please try again.');
            $button.prop('disabled', false).html('<i class="fas fa-plus-circle"></i> Enroll Now');
        });
    });

    // ==========================================
    // UTILITY FUNCTIONS
    // ==========================================
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Initialize on page load
    initializeCoursesData();
    applyFilters();
    restoreViewPreference();
    
    // ==========================================
    // NOTIFICATIONS POLLING (optional)
    // Polls an endpoint for unread notifications every 60 seconds
    // Endpoint is optional; failure is silent to avoid breaking page.
    // ==========================================
    function updateNotificationBadge(count) {
        var $badge = $('#notification-count');
        if (!count || count <= 0) {
            $badge.hide();
        } else {
            $badge.text(count).show();
        }
    }

    function fetchNotifications() {
        $.ajax({
            type: 'GET',
            url: '<?= site_url("notifications/unread") ?>',
            dataType: 'json',
            timeout: 8000,
            success: function(res) {
                if (res && res.success) {
                    updateNotificationBadge(res.count || 0);
                }
            },
            error: function() {
                // Silent failure; notifications are optional
            }
        });
    }

    // Open notifications page on click
    $('#notification-button').on('click', function() {
        window.location.href = '<?= site_url("notifications") ?>';
    });

    // Run immediately and then every 60 seconds
    try {
        fetchNotifications();
        setInterval(fetchNotifications, 60000);
    } catch (e) {
        console.warn('Notification polling not available.', e);
    }
    
    // Optional: Add keyboard shortcut to focus search
    $(document).on('keydown', function(e) {
        // Ctrl+K or Cmd+K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#client-search').focus();
        }
    });
});
</script>

<?php $this->endSection(); ?>
