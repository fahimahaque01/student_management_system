<?php
require_once 'config.php';

// Check if user is teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    $attendance = $_POST['attendance'];
    $teacher_id = $_SESSION['user_id'];
    $today = date('Y-m-d');

    $success = true;
    foreach ($attendance as $student_id => $status) {
        $query = "INSERT INTO attendance (student_id, course_id, attendance_date, status, recorded_by) 
                  VALUES ($student_id, $course_id, '$today', '$status', $teacher_id)";
        if (!mysqli_query($pdo, $query)) {
            $success = false;
        }
    }

    if ($success) {
        echo "<div class='message'>Attendance recorded successfully!</div>";
    } else {
        echo "<div class='message error'>Error recording attendance!</div>";
    }
}
?>

<div class="main-content">
    <h1>Mark Attendance</h1>

    <!-- Course Selection Form -->
    <div class="form-section">
        <h2>Select Course</h2>
        <form method="GET" action="">
            <div class="form-group">
                <label>Choose Course:</label>
                <select name="course_id" required>
                    <option value="">-- Select Course --</option>
                    <?php
                    $courses = mysqli_query($pdo, "SELECT * FROM courses ORDER BY course_code");
                    while ($course = mysqli_fetch_assoc($courses)) {
                        $selected = (isset($_GET['course_id']) && $_GET['course_id'] == $course['id']) ? 'selected' : '';
                        echo "<option value='{$course['id']}' $selected>{$course['course_code']} - {$course['course_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn-primary">Load Students</button>
        </form>
    </div>

    <?php
    // Show students if course is selected
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
        $course_id = mysqli_real_escape_string($pdo, $_GET['course_id']);
        
        // Get course info
        $course_info = mysqli_query($pdo, "SELECT * FROM courses WHERE id = $course_id");
        $course = mysqli_fetch_assoc($course_info);
        
        // Get students for this course
        $students_query = "
            SELECT s.id, s.student_id, u.full_name 
            FROM students s 
            JOIN users u ON s.user_id = u.id 
            JOIN enrollments e ON s.id = e.student_id 
            WHERE e.course_id = $course_id 
            ORDER BY u.full_name
        ";
        
        $students = mysqli_query($pdo, $students_query);
        
        if (mysqli_num_rows($students) > 0) {
    ?>
    
    <div class="form-section">
        <h2>Mark Attendance for <?php echo $course['course_code'] . ' - ' . $course['course_name']; ?></h2>
        
        <form method="POST" action="">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th style="text-align: center;">Present</th>
                        <th style="text-align: center;">Absent</th>
                        <th style="text-align: center;">Late</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = mysqli_fetch_assoc($students)): ?>
                    <tr>
                        <td class="student-id"><?php echo $student['student_id']; ?></td>
                        <td><?php echo $student['full_name']; ?></td>
                        <td style="text-align: center;">
                            <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="Present" required>
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="Absent">
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="Late">
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary">Submit Attendance</button>
            </div>
        </form>
    </div>
    
    <?php
        } else {
            echo "<div class='message error'>No students found for this course.</div>";
        }
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>