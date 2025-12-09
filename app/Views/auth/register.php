<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Supplies Inventory & Sales System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }

        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 480px;
            padding: 40px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header i {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .register-header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .register-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        .form-group.has-error .form-control {
            border-color: #dc3545;
        }

        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            color: #999;
        }

        .strength-weak { color: #dc3545; }
        .strength-fair { color: #ffc107; }
        .strength-good { color: #28a745; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h1>Register</h1>
            <p>Create your account</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <form method="POST" action="/auth/process-register" id="registerForm">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input 
                    type="text" 
                    class="form-control <?= (isset($errors['username']) ? 'is-invalid' : '') ?>" 
                    id="username" 
                    name="username" 
                    value="<?= old('username') ?>" 
                    placeholder="Choose a username (3-50 characters)"
                    minlength="3"
                    maxlength="50"
                    required
                >
                <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['username'] ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input 
                    type="email" 
                    class="form-control <?= (isset($errors['email']) ? 'is-invalid' : '') ?>" 
                    id="email" 
                    name="email" 
                    value="<?= old('email') ?>" 
                    placeholder="Enter your email"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['email'] ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input 
                    type="password" 
                    class="form-control <?= (isset($errors['password']) ? 'is-invalid' : '') ?>" 
                    id="password" 
                    name="password" 
                    placeholder="Create a strong password (min 6 characters)"
                    minlength="6"
                    onkeyup="checkPasswordStrength()"
                    required
                >
                <div class="password-strength" id="strengthText"></div>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['password'] ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password_confirm"><i class="fas fa-check"></i> Confirm Password</label>
                <input 
                    type="password" 
                    class="form-control <?= (isset($errors['password_confirm']) ? 'is-invalid' : '') ?>" 
                    id="password_confirm" 
                    name="password_confirm" 
                    placeholder="Confirm your password"
                    minlength="6"
                    required
                >
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['password_confirm'] ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-check"></i> Create Account
            </button>
        </form>

        <div class="register-footer">
            Already have an account? <a href="/auth/login">Login here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthText = document.getElementById('strengthText');
            let strength = 'Weak';
            let className = 'strength-weak';

            if (password.length < 6) {
                strengthText.textContent = '';
                return;
            }

            if (/^[a-z]+$/.test(password) || /^[A-Z]+$/.test(password) || /^\d+$/.test(password)) {
                strength = 'Weak';
                className = 'strength-weak';
            } else if (/^[a-zA-Z\d]+$/.test(password) || /^[a-zA-Z!@#$%^&*]+$/.test(password)) {
                strength = 'Fair';
                className = 'strength-fair';
            } else {
                strength = 'Strong';
                className = 'strength-good';
            }

            strengthText.textContent = `Password strength: ${strength}`;
            strengthText.className = `password-strength ${className}`;
        }
    </script>
</body>
</html>
