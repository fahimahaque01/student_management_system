<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}
include 'includes/header.php';

$teacher_id = $_SESSION['user_id'];

$total_courses = mysqli_fetch_assoc(mysqli_query($pdo, "SELECT COUNT(*) as c FROM courses WHERE teacher_id = $teacher_id"))['c'];
$total_students = mysqli_fetch_assoc(mysqli_query($pdo, "SELECT COUNT(DISTINCT e.student_id) as c FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.teacher_id = $teacher_id AND e.status='Active'"))['c'];
$today_att = mysqli_fetch_assoc(mysqli_query($pdo, "SELECT COUNT(*) as c FROM attendance a JOIN courses c ON a.course_id = c.id WHERE c.teacher_id = $teacher_id AND a.attendance_date = CURDATE()"))['c'];
?>

<div style="min-height:100vh; background:#f0f2f5; display:flex; justify-content:center; align-items:flex-start; padding-top:50px;">
    <div style="background:white; max-width:820px; width:100%; border-radius:18px; overflow:hidden; box-shadow:0 12px 35px rgba(0,0,0,0.12);">

        <!-- Header - PDF এর মতো হালকা বেগুনি থেকে গাঢ় -->
        <div style="background:linear-gradient(135deg, #9b59b6, #8e44ad); padding:45px 20px; text-align:center; color:white;">
            <h1 style="margin:0; font-size:2.7rem; font-weight:bold; font-family:Arial;">Teacher Dashboard</h1>
            <p style="margin:14px 0 0; font-size:1.3rem; opacity:0.95;">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! Manage your classes and students.
            </p>
        </div>

        <!-- Buttons - PDF এর মতো ছোট + গোলাকার -->
        <div style="padding:35px 40px 25px; text-align:center;">
            <a href="mark_attendence.php" style="background:#3498db; color:white; padding:11px 30px; margin:0 12px; border-radius:50px; text-decoration:none; font-weight:bold; font-size:1rem; display:inline-block; box-shadow:0 6px 18px rgba(52,152,219,0.35);">Mark Attendance</a>
            <a href="manage_grades.php" style="background:#3498db; color:white; padding:11px 30px; margin:0 12px; border-radius:50px; text-decoration:none; font-weight:bold; font-size:1rem; display:inline-block; box-shadow:0 6px 18px rgba(52,152,219,0.35);">Manage Grades</a>
            <a href="index.php" style="background:#3498db; color:white; padding:11px 30px; margin:0 12px; border-radius:50px; text-decoration:none; font-weight:bold; font-size:1rem; display:inline-block; box-shadow:0 6px 18px rgba(52,152,219,0.35);">View All Students & Courses</a>
        </div>

        <!-- My Courses Title -->
        <div style="text-align:center; padding:15px 0 25px;">
            <h2 style="margin:0; color:#2c3e50; font-size:1.8rem; font-weight:600;">My Courses</h2>
        </div>

        <!-- Three Cards - PDF এর মতো হালকা সাদা + ছোট -->
        <div style="display:flex; justify-content:center; gap:40px; padding:0 40px 60px; flex-wrap:wrap;">
            <div style="background:#f8f9fa; padding:28px 20px; border-radius:16px; text-align:center; width:165px; box-shadow:0 5px 18px rgba(0,0,0,0.06);">
                <p style="margin:0 0 10px; color:#7f8c8d; font-size:1.05rem; font-weight:600;">Total Courses</p>
                <h1 style="margin:0; color:#3498db; font-size:3.8rem; font-weight:bold; line-height:1;"><?php echo $total_courses; ?></h1>
            </div>
            <div style="background:#f8f9fa; padding:28px 20px; border-radius:16px; text-align:center; width:165px; box-shadow:0 5px 18px rgba(0,0,0,0.06);">
                <p style="margin:0 0 10px; color:#7f8c8d; font-size:1.05rem; font-weight:600;">Total Students</p>
                <h1 style="margin:0; color:#27ae60; font-size:3.8rem; font-weight:bold; line-height:1;"><?php echo $total_students; ?></h1>
            </div>
            <div style="background:#f8f9fa; padding:28px 20px; border-radius:16px; text-align:center; width:165px; box-shadow:0 5px 18px rgba(0,0,0,0.06);">
                <p style="margin:0 0 10px; color:#7f8c8d; font-size:1.05rem; font-weight:600;">Today's Attendance</p>
                <h1 style="margin:0; color:#e74c3c; font-size:3.8rem; font-weight:bold; line-height:1;"><?php echo $today_att; ?></h1>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>