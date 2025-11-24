<?php
require_once 'config.php';

// Check if user is teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';

$message = "";

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_grades'])) {
    $course_id = $_POST['course_id'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    $grades = $_POST['grades'];
    
    $success_count = 0;
    $error_count = 0;

    foreach ($grades as $student_id => $grade_data) {
        $grade = $grade_data['grade'];
        $grade_points = $grade_data['grade_points'];
        $remarks = $grade_data['remarks'];

        // Check if grade already exists for this student, course, semester, and year
        $check_query = "SELECT * FROM grades 
                       WHERE student_id = $student_id 
                       AND course_id = $course_id 
                       AND semester = '$semester' 
                       AND academic_year = $academic_year";
        
        $check_result = mysqli_query($pdo, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update existing grade
            $update_query = "UPDATE grades 
                            SET grade = '$grade', 
                                grade_points = $grade_points, 
                                remarks = '$remarks' 
                            WHERE student_id = $student_id 
                            AND course_id = $course_id 
                            AND semester = '$semester' 
                            AND academic_year = $academic_year";
            
            if (mysqli_query($pdo, $update_query)) {
                $success_count++;
            } else {
                $error_count++;
            }
        } else {
            // Insert new grade
            $insert_query = "INSERT INTO grades (student_id, course_id, grade, grade_points, semester, academic_year, remarks) 
                            VALUES ($student_id, $course_id, '$grade', $grade_points, '$semester', $academic_year, '$remarks')";
            
            if (mysqli_query($pdo, $insert_query)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }

    if ($error_count == 0) {
        $message = "<div class='message'>Grades submitted successfully! ($success_count records processed)</div>";
    } else {
        $message = "<div class='message error'>Grades submitted with $error_count errors. ($success_count successful)</div>";
    }
}

// Grade points mapping
$grade_points_map = [
    'A+' => 4.00,
    'A' => 3.75,
    'A-' => 3.50,
    'B+' => 3.25,
    'B' => 3.00,
    'B-' => 2.75,
    'C+' => 2.50,
    'C' => 2.25,
    'D' => 2.00,
    'F' => 0.00
];
?>

<div class="main-content">
    <h1>Manage Grades</h1>
    
    <?php echo $message; ?>

    <!-- Course Selection Form -->
    <div class="form-section">
        <h2>Select Course and Semester</h2>
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Course:</label>
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
                <div class="form-group">
                    <label>Semester:</label>
                    <select name="semester" required>
                        <option value="">-- Select Semester --</option>
                        <option value="Spring" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'Spring') ? 'selected' : ''; ?>>Spring</option>
                        <option value="Summer" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'Summer') ? 'selected' : ''; ?>>Summer</option>
                        <option value="Fall" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'Fall') ? 'selected' : ''; ?>>Fall</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Academic Year:</label>
                    <select name="academic_year" required>
                        <option value="">-- Select Year --</option>
                        <?php
                        $current_year = date('Y');
                        for ($year = $current_year - 2; $year <= $current_year + 1; $year++) {
                            $selected = (isset($_GET['academic_year']) && $_GET['academic_year'] == $year) ? 'selected' : '';
                            echo "<option value='$year' $selected>$year</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary">Load Students</button>
        </form>
    </div>

    <?php
    if (isset($_GET['course_id']) && isset($_GET['semester']) && isset($_GET['academic_year'])) {
        $course_id = mysqli_real_escape_string($pdo, $_GET['course_id']);
        $semester = mysqli_real_escape_string($pdo, $_GET['semester']);
        $academic_year = mysqli_real_escape_string($pdo, $_GET['academic_year']);
        
        // Get course info
        $course_info = mysqli_query($pdo, "SELECT * FROM courses WHERE id = $course_id");
        $course = mysqli_fetch_assoc($course_info);
        
        // Get students for this course
        $students_query = "
            SELECT s.id, s.student_id, u.full_name, 
                   g.grade, g.grade_points, g.remarks
            FROM students s 
            JOIN users u ON s.user_id = u.id 
            JOIN enrollments e ON s.id = e.student_id 
            LEFT JOIN grades g ON s.id = g.student_id 
                AND g.course_id = $course_id 
                AND g.semester = '$semester' 
                AND g.academic_year = $academic_year
            WHERE e.course_id = $course_id 
            ORDER BY u.full_name
        ";
        
        $students = mysqli_query($pdo, $students_query);
        
        if (mysqli_num_rows($students) > 0) {
    ?>
    
    <div class="form-section">
        <h2>Manage Grades for <?php echo $course['course_code'] . ' - ' . $course['course_name']; ?></h2>
        <p><strong>Semester:</strong> <?php echo $semester; ?> | <strong>Academic Year:</strong> <?php echo $academic_year; ?></p>
        
        <form method="POST" action="">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <input type="hidden" name="semester" value="<?php echo $semester; ?>">
            <input type="hidden" name="academic_year" value="<?php echo $academic_year; ?>">
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Grade</th>
                        <th>Grade Points</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = mysqli_fetch_assoc($students)): 
                        $current_grade = $student['grade'] ?? '';
                        $current_grade_points = $student['grade_points'] ?? '';
                        $current_remarks = $student['remarks'] ?? '';
                    ?>
                    <tr>
                        <td class="student-id"><?php echo $student['student_id']; ?></td>
                        <td><?php echo $student['full_name']; ?></td>
                        <td>
                            <select name="grades[<?php echo $student['id']; ?>][grade]" required 
                                    onchange="updateGradePoints(this, <?php echo $student['id']; ?>)">
                                <option value="">Select Grade</option>
                                <?php foreach ($grade_points_map as $grade_letter => $points): ?>
                                    <option value="<?php echo $grade_letter; ?>" 
                                        <?php echo ($current_grade == $grade_letter) ? 'selected' : ''; ?>
                                        data-points="<?php echo $points; ?>">
                                        <?php echo $grade_letter; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="grades[<?php echo $student['id']; ?>][grade_points]" 
                                   id="grade_points_<?php echo $student['id']; ?>" 
                                   value="<?php echo $current_grade_points; ?>" 
                                   step="0.01" min="0" max="4.00" required readonly>
                        </td>
                        <td>
                            <input type="text" name="grades[<?php echo $student['id']; ?>][remarks]" 
                                   value="<?php echo htmlspecialchars($current_remarks); ?>" 
                                   placeholder="Optional remarks">
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px;">
                <button type="submit" name="submit_grades" class="btn-primary">Submit Grades</button>
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

<script>
function updateGradePoints(selectElement, studentId) {
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var gradePoints = selectedOption.getAttribute('data-points');
    document.getElementById('grade_points_' + studentId).value = gradePoints || '';
}
</script>

<?php include 'includes/footer.php'; ?>