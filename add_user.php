<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* ====== FORM DATA ====== */
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$address = trim($_POST['address']);
$age = $_POST['age'];
$dob = $_POST['dob'];
$contact = trim($_POST['contact_number']);
$profile_image = ''; // default empty

/* ====== VALIDATION ====== */
if (empty($full_name) || empty($email) || empty($password) || empty($address) || empty($age) || empty($dob) || empty($contact)) {
    die("All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

if (!is_numeric($age)) {
    die("Age must be numeric.");
}

/* ====== CHECK EMAIL DUPLICATE ====== */
$check = $pdo->prepare("SELECT id FROM users WHERE email=?");
$check->execute([$email]);
if ($check->rowCount() > 0) {
    die("Email already registered.");
}

/* ====== HASH PASSWORD ====== */
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

/* ====== HANDLE IMAGE ====== */
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $profile_image = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "uploads/" . $profile_image);
    }
}

/* ====== INSERT USER ====== */
$stmt = $pdo->prepare("INSERT INTO users 
    (full_name,email,password,address,age,dob,contact_number,profile_image)
    VALUES (?,?,?,?,?,?,?,?)");

$stmt->execute([
    $full_name,
    $email,
    $hashed_password,
    $address,
    $age,
    $dob,
    $contact,
    $profile_image
]);

header("Location: dashboard.php?success=User added successfully!");
exit();
?>