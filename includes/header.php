<?php
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav">
                <div class="logo">Student Management System</div>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <?php if ($is_logged_in): ?>
                        <?php if ($user_role == 'admin'): ?>
                            <a href="admin_dashboard.php">Admin Dashboard</a>
                        <?php elseif ($user_role == 'teacher'): ?>
                            <a href="teacher_dashboard.php">Teacher Dashboard</a>
                        <?php elseif ($user_role == 'student'): ?>
                            <a href="student_dashboard.php">Student Dashboard</a>
                        <?php endif; ?>
                        <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>