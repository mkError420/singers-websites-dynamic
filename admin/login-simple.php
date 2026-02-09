<?php
session_start();

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        try {
            require_once __DIR__ . '/../includes/database.php';
            $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
            $user = fetchOne($sql, [$username]);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #1a1a2e 0%, #2a2a3e 100%); 
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px; 
        }
        .login-container { 
            background: rgba(255, 255, 255, 0.05); 
            padding: 3rem; 
            border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4); 
            width: 100%; 
            max-width: 400px; 
            text-align: center; 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
        .login-header h1 { 
            color: #ff6b6b; 
            margin-bottom: 1rem; 
            font-size: 2rem; 
        }
        .form-group { 
            margin-bottom: 1.5rem; 
            text-align: left; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 0.5rem; 
            color: white; 
            font-weight: 600; 
        }
        .form-control { 
            width: 100%; 
            padding: 1rem; 
            background: rgba(255, 255, 255, 0.1); 
            border: 1px solid rgba(255, 255, 255, 0.2); 
            border-radius: 8px; 
            color: white; 
            font-size: 1rem; 
            box-sizing: border-box; 
        }
        .btn { 
            width: 100%; 
            padding: 1rem; 
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4); 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 1.1rem; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.3s ease; 
        }
        .btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 16px rgba(255, 107, 107, 0.3); 
        }
        .error { 
            color: #ff6b6b; 
            background: rgba(255, 107, 107, 0.1); 
            padding: 1rem; 
            border-radius: 8px; 
            margin-bottom: 1rem; 
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <p style="margin-top: 2rem; font-size: 14px; opacity: 0.7;">
            Use credentials: admin / admin123
        </p>
    </div>
</body>
</html>
