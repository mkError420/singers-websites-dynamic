<?php
// Minimal login test without dependencies
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple check for testing
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login { background: #2a2a3e; padding: 2rem; border-radius: 10px; width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #444; background: #333; color: white; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: #ff6b6b; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login">
        <h2>Admin Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="margin-top: 20px; font-size: 12px;">
            Use: admin / admin123
        </p>
    </div>
</body>
</html>
