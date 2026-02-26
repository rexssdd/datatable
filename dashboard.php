<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-primary">User Management Dashboard</h3>
            <div>
                Welcome, <strong><?= $_SESSION['full_name']; ?></strong>
                <a href="logout.php" class="btn btn-outline-danger btn-sm ms-3">Logout</a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= $_GET['success'] ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Registered Users
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <a href="export_excel.php" class="btn btn-success btn-sm">Export Excel</a>
                    <a href="export_pdf.php" class="btn btn-danger btn-sm">Export PDF</a>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                        Add User
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="userTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Image</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Age</th>
                                <th>Date of Birth</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($user['profile_image']) && file_exists("uploads/" . $user['profile_image'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>"
                                                width="50" height="50"
                                                style="object-fit:cover;border-radius:50%;">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/50"
                                                width="50" height="50"
                                                style="border-radius:50%;">
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
                                            Update
                                        </button>
                                        <a href="delete_user.php?id=<?= $user['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure?')">
                                            Delete
                                        </a>
                                    </td>

                                    <!-- UPDATE MODAL -->
                                    <div class="modal fade" id="editModal<?= $user['id'] ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <form action="update_user.php" method="POST" enctype="multipart/form-data">

                                                    <div class="modal-header bg-warning">
                                                        <h5>Update User</h5>
                                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">

                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">

                                                        <div class="mb-2 text-center">
                                                            <img src="uploads/<?= $user['profile_image'] ?>"
                                                                width="80" height="80"
                                                                style="border-radius:50%;object-fit:cover;">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label>Change Image</label>
                                                            <input type="file" name="profile_image" class="form-control">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label>Full Name</label>
                                                            <input type="text" name="full_name" class="form-control"
                                                                value="<?= $user['full_name'] ?>" required>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label>Address</label>
                                                            <input type="text" name="address" class="form-control"
                                                                value="<?= $user['address'] ?>" required>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label>Age</label>
                                                            <input type="number" name="age" class="form-control"
                                                                value="<?= $user['age'] ?>" required>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label>Contact</label>
                                                            <input type="text" name="contact_number" class="form-control"
                                                                value="<?= $user['contact_number'] ?>" required>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button class="btn btn-warning">Update</button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


    <!-- ADD USER MODAL -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_user.php" method="POST" enctype="multipart/form-data">

                    <div class="modal-header bg-success text-white">
                        <h5>Add New User</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-2">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Age</label>
                            <input type="number" name="age" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Contact</label>
                            <input type="text" name="contact_number" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Profile Image</label>
                            <input type="file" name="profile_image" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">Save User</button>
                    </div>

                </form>
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