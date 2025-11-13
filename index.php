<?php
require_once 'config.php'; // includes database connection
include 'includes/header.php';


?>

<!-- University Header -->
<div class="welcome-section">
    <h1>Welcome to Student Management System</h1>
    <p>North East University Bangladesh<br>Department of Computer Science and Engineering</p>
</div>

<!-- Demo Accounts -->
<div class="demo-accounts">
    <h3>Demo Accounts:</h3>
    <p><strong>Admin:</strong> admin</p>
    <p><strong>Teacher:</strong> teacher </p>
    <p><strong>Student:</strong> student </p>
</div>

<!-- Students and Their Courses Section -->
<h2 class="text-center">Students and Their Courses</h2>

<?php
// Query to get all students and their courses
$query = "
    SELECT 
        s.student_id,
        u.full_name AS student_name,
        c.course_code,
        c.course_name
    FROM students s
    JOIN users u ON s.user_id = u.id
    JOIN enrollments e ON s.id = e.student_id
    JOIN courses c ON e.course_id = c.id
    ORDER BY u.full_name, c.course_code
";

$result = mysqli_query($pdo, $query);

// Fetch all rows as associative array
$student_courses = [];
if ($result && mysqli_num_rows($result) > 0) {
    $student_courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<?php if (!empty($student_courses)): ?>
<table class="data-table">
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Course Code</th>
            <th>Course Name</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($student_courses as $row): ?>
        <tr>
            <td class="student-id"><?php echo htmlspecialchars($row['student_id']); ?></td>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['course_code']); ?></td>
            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p class="text-center">No student data found. Please check your database.</p>
<?php endif; ?>


<!-- Grouped Courses by Student -->
<h2 class="text-center">Students with All Courses</h2>

<?php
// Query to group courses by student
$query2 = "
    SELECT 
        s.student_id,
        u.full_name AS student_name,
        GROUP_CONCAT(CONCAT(c.course_code, ' - ', c.course_name) SEPARATOR ' | ') AS courses
    FROM students s
    JOIN users u ON s.user_id = u.id
    JOIN enrollments e ON s.id = e.student_id
    JOIN courses c ON e.course_id = c.id
    GROUP BY s.id, s.student_id, u.full_name
    ORDER BY u.full_name
";

$result2 = mysqli_query($pdo, $query2);
$students_grouped = [];

if ($result2 && mysqli_num_rows($result2) > 0) {
    $students_grouped = mysqli_fetch_all($result2, MYSQLI_ASSOC);
}
?>

<?php if (!empty($students_grouped)): ?>
<table class="data-table">
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>All Courses</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students_grouped as $student): ?>
        <tr>
            <td class="student-id"><?php echo htmlspecialchars($student['student_id']); ?></td>
            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
            <td>
                <?php
                $courses = explode(' | ', $student['courses']);
                foreach ($courses as $course):
                ?>
                    <span class="course-badge"><?php echo htmlspecialchars($course); ?></span>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
