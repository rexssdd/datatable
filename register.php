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
    if (
        empty($full_name) || empty($email) || empty($password) ||
        empty($address) || empty($age) || empty($dob) || empty($contact)
    ) {

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .form-control {
            border-radius: 10px;
            padding-left: 40px;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
        }

        .btn-success {
            border-radius: 30px;
            padding: 10px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .btn-success:hover {
            transform: scale(1.03);
        }

        .profile-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            display: none;
            margin: 10px auto;
            border: 3px solid #0072ff;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-lg p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">Create Account</h3>
                        <p class="text-muted">Join us today! It only takes a minute.</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success text-center">
                            <?= $success ?>
                            <br>
                            <a href="login.php" class="btn btn-primary btn-sm mt-2">Go to Login</a>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
                                    <input type="text" name="address" class="form-control" placeholder="Address" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    <input type="number" name="age" class="form-control" placeholder="Age" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="date" name="dob" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                    <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3 text-center">
                                <label class="form-label fw-semibold">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*" onchange="previewImage(event)" required>
                                <img id="preview" class="profile-preview">
                            </div>

                        </div>

                        <button class="btn btn-success w-100 mt-3">Register</button>

                        <div class="text-center mt-4">
                            Already have an account?
                            <a href="login.php" class="fw-semibold text-decoration-none">Login here</a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = "block";
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
