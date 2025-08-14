<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BC Attendance System</title>
    
    <!-- Material Design 3 CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <span class="material-icons logo-icon">how_to_reg</span>
                </div>
                <h1 class="auth-title">BC Attendance System</h1>
                <p class="auth-subtitle">Sign in to your account</p>
            </div>
            
            <?php if (isset($errors) && !empty($errors)): ?>
            <div class="auth-errors">
                <?php foreach ($errors as $error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="/login" class="auth-form">
                <?= \App\Core\CSRF::getTokenField() ?>
                
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-wrapper">
                        <span class="material-icons input-icon">person</span>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="<?= htmlspecialchars($oldInput['username'] ?? '') ?>"
                            class="form-input" 
                            placeholder="Enter your username"
                            required
                            autofocus
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="material-icons input-icon">lock</span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Enter your password"
                            required
                        >
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <span class="material-icons">visibility</span>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="auth-button">
                        <span class="material-icons">login</span>
                        Sign In
                    </button>
                </div>
            </form>
            
            <div class="auth-footer">
                <p>&copy; <?= date('Y') ?> BC Attendance System. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <script>
        // Password visibility toggle
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('.material-icons');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        });
        
        // Auto-focus username field
        document.getElementById('username').focus();
    </script>
</body>
</html>
