<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address']);
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $contact = trim($_POST['contact_number']);

    // ===== VALIDATION =====
    if (empty($full_name) || empty($email) || empty($password) || 
        empty($address) || empty($age) || empty($dob) || empty($contact)) {

        $error = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Invalid email format.";

    } elseif (!is_numeric($age)) {

        $error = "Age must be numeric.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        // ===== CHECK EMAIL =====
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {

            $error = "Email already registered.";

        } else {

            // ===== HANDLE IMAGE UPLOAD =====
            $imageName = null;

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {

                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $_FILES['profile_image']['name'];
                $fileTmp = $_FILES['profile_image']['tmp_name'];
                $fileSize = $_FILES['profile_image']['size'];

                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $error = "Only JPG, JPEG, PNG, GIF files are allowed.";
                } elseif ($fileSize > 2 * 1024 * 1024) {
                    $error = "Image size must be less than 2MB.";
                } else {
                    $imageName = time() . "_" . uniqid() . "." . $ext;
                    move_uploaded_file($fileTmp, "uploads/" . $imageName);
                }
            } else {
                $error = "Profile image is required.";
            }

            // ===== INSERT USER =====
            if (!isset($error)) {

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users 
                    (full_name, email, password, address, age, dob, contact_number, profile_image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $full_name,
                    $email,
                    $hashed_password,
                    $address,
                    $age,
                    $dob,
                    $contact,
                    $imageName
                ]);

                $success = "Registration successful! You can now login.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card shadow">
<div class="card-header bg-success text-white text-center">
<h4>Create Account</h4>
</div>

<div class="card-body">

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if(isset($success)): ?>
<div class="alert alert-success">
<?= $success ?>
<br>
<a href="login.php" class="btn btn-primary btn-sm mt-2">Go to Login</a>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>Full Name</label>
<input type="text" name="full_name" class="form-control" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label>Confirm Password</label>
<input type="password" name="confirm_password" class="form-control" required>
</div>

<div class="mb-3">
<label>Address</label>
<input type="text" name="address" class="form-control" required>
</div>

<div class="mb-3">
<label>Age</label>
<input type="number" name="age" class="form-control" required>
</div>

<div class="mb-3">
<label>Date of Birth</label>
<input type="date" name="dob" class="form-control" required>
</div>

<div class="mb-3">
<label>Contact Number</label>
<input type="text" name="contact_number" class="form-control" required>
</div>

<div class="mb-3">
<label>Profile Image</label>
<input type="file" name="profile_image" class="form-control" accept="image/*" required>
</div>

<button class="btn btn-success w-100">Register</button>

<div class="text-center mt-3">
Already have an account?
<a href="login.php">Login here</a>
</div>

</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>