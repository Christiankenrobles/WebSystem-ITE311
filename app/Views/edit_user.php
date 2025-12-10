<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Edit User</h2>
            <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
        <hr>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-4">User Information</h5>

                    <form method="POST" action="<?= base_url('users/update/' . $user['id']) ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= esc($user['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= esc($user['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password (Leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter new password if you want to change it">
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                   placeholder="Confirm new password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-4">User Details</h5>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>User ID:</strong>
                        </div>
                        <div class="col-sm-7">
                            <span><?= esc($user['id']) ?></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Current Role:</strong>
                        </div>
                        <div class="col-sm-7">
                            <span class="badge 
                                <?= $user['role'] === 'admin' ? 'bg-danger' : 
                                    ($user['role'] === 'teacher' ? 'bg-warning text-dark' : 'bg-success') ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-sm-7">
                            <span><?= esc(date('M d, Y H:i', strtotime($user['created_at']))) ?></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-sm-7">
                            <span><?= esc(date('M d, Y H:i', strtotime($user['updated_at']))) ?></span>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Changes will be saved immediately. The user can log in with their updated credentials.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password confirmation validation
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');

        function validatePasswords() {
            if (passwordInput.value && passwordInput.value !== passwordConfirmInput.value) {
                passwordConfirmInput.classList.add('is-invalid');
                return false;
            } else {
                passwordConfirmInput.classList.remove('is-invalid');
                return true;
            }
        }

        passwordInput.addEventListener('input', validatePasswords);
        passwordConfirmInput.addEventListener('input', validatePasswords);

        document.querySelector('form').addEventListener('submit', function(e) {
            if (passwordInput.value || passwordConfirmInput.value) {
                if (!validatePasswords()) {
                    e.preventDefault();
                    alert('Passwords do not match');
                }
                if (passwordInput.value.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long');
                }
            }
        });
    </script>

<?= $this->endSection() ?>
