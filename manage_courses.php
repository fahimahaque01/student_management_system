<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle Add Course
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_course'])) {
    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $credits = trim($_POST['credits']);
    $description = trim($_POST['description']);

    if ($course_code && $course_name) {
        // Check if course code exists
        $check = mysqli_query($pdo, "SELECT * FROM courses WHERE course_code='$course_code'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Course code already exists!";
        } else {
            $query = "INSERT INTO courses (course_code, course_name, credits, description) 
                     VALUES ('$course_code', '$course_name', $credits, '$description')";
            
            if (mysqli_query($pdo, $query)) {
                $message = "Course added successfully!";
            } else {
                $message = "Error adding course: " . mysqli_error($pdo);
            }
        }
    } else {
        $message = "Course code and name are required!";
    }
}

// Handle Delete Course
if (isset($_GET['delete_course'])) {
    $course_id = $_GET['delete_course'];
    $delete_query = "DELETE FROM courses WHERE id = $course_id";
    if (mysqli_query($pdo, $delete_query)) {
        $message = "Course deleted successfully!";
    } else {
        $message = "Error deleting course: " . mysqli_error($pdo);
    }
}

// Fetch all courses
$courses_query = "SELECT * FROM courses ORDER BY course_code";
$courses_result = mysqli_query($pdo, $courses_query);
$courses = [];
if ($courses_result && mysqli_num_rows($courses_result) > 0) {
    $courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);
}

include 'includes/header.php';
?>

<div class="main-content">
    <h1>Manage Courses</h1>
    
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Add Course Form -->
    <div class="form-section">
        <h2>Add New Course</h2>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Course Code *</label>
                    <input type="text" name="course_code" required>
                </div>
                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" name="course_name" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Credits</label>
                    <input type="number" name="credits" value="3" min="1" max="10">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
            </div>

            <button type="submit" name="add_course" class="btn-primary">Add Course</button>
        </form>
    </div>

    <!-- Courses List -->
    <div class="list-section">
        <h2>All Courses</h2>
        <?php if (!empty($courses)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['credits']); ?></td>
                    <td><?php echo htmlspecialchars($course['description']); ?></td>
                    <td>
                        <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn-edit">Edit</a>
                        <a href="manage_courses.php?delete_course=<?php echo $course['id']; ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No courses found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>