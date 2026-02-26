<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in user image
$userStmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);

$navImage = "https://via.placeholder.com/40";
if (!empty($currentUser['profile_image']) && file_exists("uploads/" . $currentUser['profile_image'])) {
    $navImage = "uploads/" . $currentUser['profile_image'];
}

// Fetch all users
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}

/* Navbar */
.topbar {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    padding: 15px 25px;
    color: white;
    border-radius: 0 0 20px 20px;
}

/* Cards */
.stat-card {
    border-radius: 15px;
    color: white;
    padding: 20px;
    transition: 0.3s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.card-users { background: #4e73df; }

/* Table */
.table thead {
    background: #4e73df;
    color: white;
}
.table tbody tr:hover {
    background-color: #f1f3f9;
}

/* Buttons */
.btn-sm {
    border-radius: 20px;
}
.btn-warning { color: white; }

.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
}
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar d-flex justify-content-between align-items-center">

    <!-- Left Side -->
    <h4 class="mb-0 fw-semibold">
        <i class="fa fa-chart-line me-2"></i> Admin Dashboard
    </h4>

    <!-- Right Side -->
    <div class="d-flex align-items-center gap-3">

        <!-- Profile Image -->
        <img src="<?= $navImage ?>"
             alt="Profile"
             class="rounded-circle"
             width="40"
             height="40"
             style="object-fit: cover; border: 2px solid #fff;">

        <!-- User Name -->
        <span class="fw-medium">
            Welcome, 
            <strong><?= htmlspecialchars($_SESSION['full_name']); ?></strong>
        </span>

        <!-- Logout -->
        <a href="logout.php" class="btn btn-light btn-sm px-3">
            <i class="fa fa-sign-out-alt me-1"></i> Logout
        </a>

    </div>
</div>

<div class="container py-4">

    <!-- STATS -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card card-users shadow">
                <h5>Total Users</h5>
                <h2><?= count($users); ?></h2>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= $_GET['success'] ?></div>
    <?php endif; ?>

    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Registered Users</h5>
            <div>
                <a href="export_excel.php" class="btn btn-success btn-sm me-2">
                    <i class="fa fa-file-excel"></i> Excel
                </a>
                <a href="export_pdf.php" class="btn btn-danger btn-sm me-2">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-user-plus"></i> Add User
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="userTable" class="table align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Age</th>
                            <th>DOB</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?php if (!empty($user['profile_image']) && file_exists("uploads/" . $user['profile_image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" class="avatar">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/50" class="avatar">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['address']) ?></td>
                            <td><?= htmlspecialchars($user['age']) ?></td>
                            <td><?= htmlspecialchars($user['dob']) ?></td>
                            <td><?= htmlspecialchars($user['contact_number']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $user['id'] ?>">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <a href="delete_user.php?id=<?= $user['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this user?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#userTable').DataTable();
        });
    </script>

</body>

</html>
