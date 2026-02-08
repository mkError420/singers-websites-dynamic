<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Start secure session
start_secure_session();

// Redirect if already logged in
if (is_logged_in()) {
    redirect('admin/dashboard.php');
}

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Security token expired. Please try again.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        // Check user credentials
        $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
        $user = fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            
            // Redirect to dashboard
            $redirect_url = $_SESSION['redirect_url'] ?? APP_URL . '/admin/dashboard.php';
            unset($_SESSION['redirect_url']);
            redirect($redirect_url);
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background: var(--dark-secondary);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        
        .login-header p {
            color: var(--text-secondary);
        }
        
        .login-form {
            text-align: left;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            background: var(--dark-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background: var(--error-color);
            color: white;
        }
        
        .back-link {
            margin-top: 2rem;
            color: var(--text-secondary);
        }
        
        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .back-link a:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p><?php echo APP_NAME; ?> Management Panel</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="back-link">
            <a href="<?php echo APP_URL; ?>/index.php">
                <i class="fas fa-arrow-left"></i> Back to Website
            </a>
        </div>
    </div>
</body>
</html>
