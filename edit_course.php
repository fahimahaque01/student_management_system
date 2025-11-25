<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// Get course ID from URL
if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = $_GET['id'];

// Fetch course data
$course_query = "SELECT * FROM courses WHERE id = $course_id";
$course_result = mysqli_query($pdo, $course_query);

if (!$course_result || mysqli_num_rows($course_result) === 0) {
    $message = "Course not found!";
    header("Location: manage_courses.php");
    exit;
}

$course = mysqli_fetch_assoc($course_result);

// Handle Update Course
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_course'])) {
    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $credits = trim($_POST['credits']);
    $description = trim($_POST['description']);
    $teacher_id = $_POST['teacher_id'] ? $_POST['teacher_id'] : 'NULL';

    if ($course_code && $course_name) {
        // Check if course code already exists (excluding current course)
        $check_course = mysqli_query($pdo, "SELECT * FROM courses WHERE course_code='$course_code' AND id != $course_id");
        if (mysqli_num_rows($check_course) > 0) {
            $message = "Course code already exists!";
        } else {
            $update_query = "UPDATE courses SET 
                            course_code = '$course_code', 
                            course_name = '$course_name', 
                            credits = $credits, 
                            description = '$description',
                            teacher_id = $teacher_id 
                            WHERE id = $course_id";
            
            if (mysqli_query($pdo, $update_query)) {
                $message = "Course updated successfully!";
                // Refresh course data
                $course_result = mysqli_query($pdo, $course_query);
                $course = mysqli_fetch_assoc($course_result);
            } else {
                $message = "Error updating course: " . mysqli_error($pdo);
            }
        }
    } else {
        $message = "Course code and name are required!";
    }
}

include 'includes/header.php';
?>

<div class="main-content">
    <h1>Edit Course</h1>
    
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="form-section">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Course Code *</label>
                    <input type="text" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Credits</label>
                    <input type="number" name="credits" value="<?php echo htmlspecialchars($course['credits']); ?>" min="1" max="10">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Assign Teacher</label>
                    <select name="teacher_id">
                        <option value="">Select Teacher</option>
                        <?php
                        $teachers_query = mysqli_query($pdo, "SELECT id, full_name FROM users WHERE role='teacher'");
                        while ($teacher = mysqli_fetch_assoc($teachers_query)) {
                            $selected = ($teacher['id'] == $course['teacher_id']) ? 'selected' : '';
                            echo "<option value='{$teacher['id']}' $selected>{$teacher['full_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" name="update_course" class="btn-primary">Update Course</button>
                <a href="manage_courses.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>