<?php
require_once 'config.php';

// Check if user is teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';
?>

<div class="main-content">
    <div class="admin-welcome">
        <h1>Teacher Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?>! Manage your classes and students.</p>
    </div>

    <div class="dashboard-links">
        <a href="mark_attendence.php" class="dashboard-link">
            Mark Attendance
        </a>
    
        <a href="manage_grades.php" class="dashboard-link">
             Manage Grades
        </a>
        <a href="index.php" class="dashboard-link">
             View All Students & Courses
        </a>
    </div>

    <!-- Quick Stats Section -->
    <div class="stats-section">
        <h2>My Courses</h2>
        <div class="stats-container">
            <?php
            // Get teacher's courses count
            $courses_result = mysqli_query($pdo, "SELECT COUNT(*) as count FROM courses");
            $courses_row = mysqli_fetch_assoc($courses_result);
            $courses_count = $courses_row['count'];
            
            // Get total students count
            $students_result = mysqli_query($pdo, "SELECT COUNT(*) as count FROM students");
            $students_row = mysqli_fetch_assoc($students_result);
            $students_count = $students_row['count'];
            
            // Get today's attendance count (you can modify this query based on your needs)
            $today = date('Y-m-d');
            $attendance_result = mysqli_query($pdo, "SELECT COUNT(*) as count FROM attendance WHERE attendance_date = '$today'");
            $attendance_row = mysqli_fetch_assoc($attendance_result);
            $attendance_count = $attendance_row['count'];
            ?>
            <div class="stat-card">
                <h3>Total Courses</h3>
                <div class="stat-number stat-courses"><?php echo $courses_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="stat-number stat-students"><?php echo $students_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Today's Attendance</h3>
                <div class="stat-number stat-users"><?php echo $attendance_count; ?></div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>