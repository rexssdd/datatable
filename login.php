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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(-45deg, #6c5ce7, #00b894, #0984e3, #fd79a8);
    background-size: 400% 400%;
    animation: gradientMove 10s ease infinite;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
}

@keyframes gradientMove {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
}

.login-card {
    width: 100%;
    max-width: 420px;
    padding: 40px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    color: white;
}

.form-control {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    border-radius: 12px;
}

.form-control::placeholder {
    color: rgba(255,255,255,0.7);
}

.form-control:focus {
    background: rgba(255,255,255,0.3);
    box-shadow: none;
    color: white;
}

.btn-login {
    background: white;
    color: #6c5ce7;
    font-weight: 600;
    border-radius: 30px;
    transition: 0.3s;
}

.btn-login:hover {
    transform: scale(1.05);
    background: #f1f1f1;
}

.social-btn {
    border-radius: 30px;
    font-size: 14px;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 12px;
    cursor: pointer;
    color: white;
}

.input-wrapper {
    position: relative;
}

a {
    color: #fff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="login-card text-center">
    <h2 class="fw-bold mb-2">Welcome Back 👋</h2>
    <p class="mb-4">Sign in to continue</p>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3 input-wrapper">
            <input type="email" name="email" class="form-control py-2" placeholder="Email Address" required>
        </div>

        <div class="mb-3 input-wrapper">
            <input type="password" name="password" id="password" class="form-control py-2" placeholder="Password" required>
            <i class="fa fa-eye toggle-password" onclick="togglePassword()"></i>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <div>
                <input type="checkbox" id="remember">
                <label for="remember"> Remember me</label>
            </div>
            <a href="#">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-login w-100 py-2">Login</button>
    </form>

    <div class="my-3 text-light">or</div>

    <!-- Social buttons (UI only) -->
    <button class="btn btn-danger w-100 social-btn mb-2">
        <i class="fab fa-google me-2"></i> Continue with Google
    </button>

    <button class="btn btn-primary w-100 social-btn">
        <i class="fab fa-facebook-f me-2"></i> Continue with Facebook
    </button>

    <div class="mt-4">
        Don't have an account? 
        <a href="register.php" class="fw-bold">Register</a>
    </div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    password.type = password.type === "password" ? "text" : "password";
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
