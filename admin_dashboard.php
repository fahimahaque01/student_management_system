<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';
?>

<div class="main-content">
    <div class="admin-welcome">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?>! You have full control over the system.</p>
    </div>

    <div class="dashboard-links">
        <a href="manage_students.php" class="dashboard-link">
             Manage Students
        </a>
        <a href="manage_courses.php" class="dashboard-link">
             Manage Courses
        </a>
        <a href="index.php" class="dashboard-link">
             View All Students & Courses
        </a>
    </div>

    <!-- Quick Stats Section -->
    <div class="stats-section">
        <h2>Quick Stats</h2>
        <div class="stats-container">
            <?php
            // Get counts from database
            $students_result = mysqli_query($pdo, "SELECT COUNT(*) as count FROM students");
            $students_row = mysqli_fetch_assoc($students_result);
            $students_count = $students_row['count'];
            
            $courses_result = mysqli_query($pdo, "SELECT COUNT(*) as count FROM courses");
            $courses_row = mysqli_fetch_assoc($courses_result);
            $courses_count = $courses_row['count'];
            
            $users_result = mysqli_query($pdo, "SELECT COUNT(*) as count FROM users");
            $users_row = mysqli_fetch_assoc($users_result);
            $users_count = $users_row['count'];
            ?>
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="stat-number stat-students"><?php echo $students_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Courses</h3>
                <div class="stat-number stat-courses"><?php echo $courses_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-number stat-users"><?php echo $users_count; ?></div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>