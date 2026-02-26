<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6c5ce7, #00b894);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-left {
            background: url('https://source.unsplash.com/400x500/?technology,login') no-repeat center center;
            background-size: cover;
        }
        .login-right {
            padding: 30px;
        }
        .login-right h2 {
            color: #2d3436;
            margin-bottom: 20px;
        }
        .btn-login {
            background: #6c5ce7;
            color: white;
        }
        .btn-login:hover {
            background: #341f97;
        }
        .alert {
            font-size: 0.9rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #6c5ce7;
        }
    </style>
</head>
<body>

<div class="card login-card shadow-lg" style="max-width: 900px; width: 100%; display: flex;">
    <div class="login-left d-none d-md-block col-md-5"></div>
    <div class="login-right col-12 col-md-7">
        <h2 class="text-center fw-bold">Welcome Back</h2>
        <p class="text-center text-muted mb-4">Sign in to continue</p>

        <?php if(isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <input type="checkbox" name="remember" id="remember"> <label for="remember">Remember me</label>
                </div>
                <a href="#" class="text-decoration-none">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-login w-100 py-2">Login</button>
        </form>

        <div class="text-center mt-3">
            Don't have an account? <a href="register.php" class="text-decoration-none fw-bold">Register</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
