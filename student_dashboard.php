<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// 1. Student Name + ID
$user_q = mysqli_query($pdo, "SELECT full_name FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_q);

$stu_q = mysqli_query($pdo, "SELECT student_id FROM students WHERE user_id = $user_id");
$stu = mysqli_fetch_assoc($stu_q);
$student_id = $stu['student_id'] ?? 'Not Assigned';

// 2. Enrolled Courses
$courses_q = mysqli_query($pdo, "
    SELECT c.course_code, c.course_name, c.credits 
    FROM courses c 
    JOIN enrollments e ON c.id = e.course_id 
    JOIN students s ON e.student_id = s.id 
    WHERE s.user_id = $user_id AND e.status = 'Active'
");

// 3. Attendance Summary
$attendance_q = mysqli_query($pdo, "
    SELECT c.course_name,
           COUNT(a.status) as total_classes,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present
    FROM courses c
    JOIN enrollments e ON c.id = e.course_id
    JOIN students s ON e.student_id = s.id
    LEFT JOIN attendance a ON a.course_id = c.id AND a.student_id = s.id
    WHERE s.user_id = $user_id
    GROUP BY c.id
");

// 4. Grades
$grades_q = mysqli_query($pdo, "
    SELECT c.course_name, g.grade 
    FROM grades g
    JOIN courses c ON g.course_id = c.id
    JOIN students s ON g.student_id = s.id
    WHERE s.user_id = $user_id
");
?>

<div style="min-height:100vh; background:#f4f6f9; padding:40px 20px;">
    <div style="max-width:900px; margin:0 auto; background:white; border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1);">

        <!-- Welcome Header -->
        <div style="background:linear-gradient(135deg,#3498db,#2980b9); color:white; padding:40px; text-align:center;">
            <h1 style="margin:0; font-size:2.6rem;">Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
            <p style="margin:10px 0 0; font-size:1.3rem;">Student ID: <?php echo htmlspecialchars($student_id); ?></p>
        </div>

        <div style="padding:40px; display:grid; gap:30px;">

            <!-- My Courses -->
            <div style="background:#f8f9fa; padding:25px; border-radius:12px; border-left:5px solid #3498db;">
                <h2 style="margin:0 0 15px; color:#2c3e50;">My Enrolled Courses</h2>
                <?php if (mysqli_num_rows($courses_q) > 0): ?>
                    <div style="display:grid; gap:15px;">
                        <?php while($c = mysqli_fetch_assoc($courses_q)): ?>
                            <div style="background:white; padding:15px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                                <strong><?php echo htmlspecialchars($c['course_code']); ?></strong> - <?php echo htmlspecialchars($c['course_name']); ?>
                                <span style="float:right; color:#7f8c8d;"><?php echo $c['credits']; ?> Credits</span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="color:#95a5a6; font-style:italic;">No courses enrolled yet. Please contact administration.</p>
                <?php endif; ?>
            </div>

            <!-- Attendance Summary -->
            <div style="background:#f8f9fa; padding:25px; border-radius:12px; border-left:5px solid #27ae60;">
                <h2 style="margin:0 0 15px; color:#2c3e50;">Attendance Summary</h2>
                <?php if (mysqli_num_rows($attendance_q) > 0): ?>
                    <table style="width:100%; border-collapse:collapse;">
                        <?php while($a = mysqli_fetch_assoc($attendance_q)): ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?php echo htmlspecialchars($a['course_name']); ?></td>
                            <td style="padding:12px; text-align:right; font-weight:bold; color:#27ae60; font-size:1.2rem;">
                                <?php echo $a['total_classes'] > 0 ? round(($a['present']/$a['total_classes'])*100) . '%' : 'N/A'; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p style="color:#95a5a6; font-style:italic;">No attendance records yet.</p>
                <?php endif; ?>
            </div>

            <!-- My Grades -->
            <div style="background:#f8f9fa; padding:25px; border-radius:12px; border-left:5px solid #9b59b6;">
                <h2 style="margin:0 0 15px; color:#2c3e50;">My Grades</h2>
                <?php if (mysqli_num_rows($grades_q) > 0): ?>
                    <table style="width:100%; border-collapse:collapse;">
                        <?php while($g = mysqli_fetch_assoc($grades_q)): ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?php echo htmlspecialchars($g['course_name']); ?></td>
                            <td style="padding:12px; text-align:right; font-weight:bold; color:#9b59b6; font-size:1.5rem;">
                                <?php echo htmlspecialchars($g['grade']); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p style="color:#95a5a6; font-style:italic;">No grades published yet.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>