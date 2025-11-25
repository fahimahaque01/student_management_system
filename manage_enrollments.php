<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle Enroll Student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enroll_student'])) {
    $student_id = trim($_POST['student_id']);
    $course_id = trim($_POST['course_id']);

    if ($student_id && $course_id) {
        // Check if enrollment already exists
        $check_enrollment = mysqli_query($pdo, 
            "SELECT * FROM enrollments 
             WHERE student_id = $student_id AND course_id = $course_id AND status = 'Active'");
        
        if (mysqli_num_rows($check_enrollment) > 0) {
            $message = "Student is already enrolled in this course!";
        } else {
            $enroll_query = "INSERT INTO enrollments (student_id, course_id, status) 
                            VALUES ($student_id, $course_id, 'Active')";
            
            if (mysqli_query($pdo, $enroll_query)) {
                $message = "Student enrolled successfully!";
            } else {
                $message = "Error enrolling student: " . mysqli_error($pdo);
            }
        }
    } else {
        $message = "Please select both student and course!";
    }
}

// Handle Drop Enrollment
if (isset($_GET['drop_enrollment'])) {
    $enrollment_id = $_GET['drop_enrollment'];
    $drop_query = "UPDATE enrollments SET status = 'Dropped' WHERE id = $enrollment_id";
    
    if (mysqli_query($pdo, $drop_query)) {
        $message = "Enrollment dropped successfully!";
    } else {
        $message = "Error dropping enrollment: " . mysqli_error($pdo);
    }
}

// Fetch all students for dropdown
$students_query = "
    SELECT s.id, u.full_name, s.student_id 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE u.role = 'student' 
    ORDER BY u.full_name
";
$students_result = mysqli_query($pdo, $students_query);
$students = [];
if ($students_result) {
    $students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);
}

// Fetch all courses for dropdown
$courses_query = "SELECT id, course_code, course_name FROM courses ORDER BY course_code";
$courses_result = mysqli_query($pdo, $courses_query);
$courses = [];
if ($courses_result) {
    $courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);
}

// Fetch current enrollments - UPDATED QUERY without enrollment_date
$enrollments_query = "
    SELECT e.id, e.status,
           u.full_name, s.student_id,
           c.course_code, c.course_name
    FROM enrollments e
    JOIN students s ON e.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    WHERE e.status = 'Active'
    ORDER BY u.full_name, c.course_code
";
$enrollments_result = mysqli_query($pdo, $enrollments_query);
$enrollments = [];
if ($enrollments_result && mysqli_num_rows($enrollments_result) > 0) {
    $enrollments = mysqli_fetch_all($enrollments_result, MYSQLI_ASSOC);
}

include 'includes/header.php';
?>

<div class="main-content">
    <h1>Manage Course Enrollments</h1>
    
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Enroll Student Form -->
    <div class="form-section">
        <h2>Enroll Student in Course</h2>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Select Student *</label>
                    <select name="student_id" required>
                        <option value="">Choose Student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>">
                                <?php echo htmlspecialchars($student['full_name'] . ' (' . $student['student_id'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Course *</label>
                    <select name="course_id" required>
                        <option value="">Choose Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>">
                                <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="enroll_student" class="btn-primary">Enroll Student</button>
        </form>
    </div>

    <!-- Current Enrollments -->
    <div class="list-section">
        <h2>Current Enrollments</h2>
        <?php if (!empty($enrollments)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($enrollment['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($enrollment['student_id']); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($enrollment['course_code']); ?></strong><br>
                        <small><?php echo htmlspecialchars($enrollment['course_name']); ?></small>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($enrollment['status']); ?>">
                            <?php echo $enrollment['status']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="manage_enrollments.php?drop_enrollment=<?php echo $enrollment['id']; ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Are you sure you want to drop this enrollment?')">
                           Drop
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No active enrollments found.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-completed {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-dropped {
    background-color: #f8d7da;
    color: #721c24;
}
</style>

<?php include 'includes/footer.php'; ?>