<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_POST['id'];

// GET EXISTING USER
$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
$profile_image = $user['profile_image']; // keep old image if none uploaded

// GET FORM DATA
$full_name = trim($_POST['full_name']);
$address = trim($_POST['address']);
$age = $_POST['age'];
$dob = $_POST['dob'];
$contact = trim($_POST['contact_number']);

// VALIDATE
if (!is_numeric($age)) die("Age must be numeric.");

// HANDLE IMAGE UPLOAD
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed)) {
        $new_image = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "uploads/" . $new_image);
        $profile_image = $new_image;

        // Optionally delete old image to save space
        if (!empty($user['profile_image']) && file_exists("uploads/" . $user['profile_image'])) {
            unlink("uploads/" . $user['profile_image']);
        }
    }
}

// UPDATE USER
$update = $pdo->prepare("UPDATE users 
    SET full_name=?, address=?, age=?, dob=?, contact_number=?, profile_image=? 
    WHERE id=?");

$update->execute([$full_name, $address, $age, $dob, $contact, $profile_image, $id]);

header("Location: dashboard.php?success=User updated successfully!");
exit();