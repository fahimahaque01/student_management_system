<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// Get student ID from URL
if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit;
}

$student_user_id = $_GET['id'];

// Fetch student data
$student_query = "
    SELECT u.id, u.full_name, u.username, u.email, s.student_id, s.phone, s.address, s.date_of_birth, s.gender
    FROM users u 
    JOIN students s ON u.id = s.user_id 
    WHERE u.id = $student_user_id
";
$student_result = mysqli_query($pdo, $student_query);

if (!$student_result || mysqli_num_rows($student_result) === 0) {
    $message = "Student not found!";
    header("Location: manage_students.php");
    exit;
}

$student = mysqli_fetch_assoc($student_result);

// Handle Update Student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_student'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $gender = trim($_POST['gender']);

    if ($fullname && $username && $student_id) {
        // Check if username already exists (excluding current student)
        $check_username = mysqli_query($pdo, "SELECT * FROM users WHERE username='$username' AND id != $student_user_id");
        if (mysqli_num_rows($check_username) > 0) {
            $message = "Username already exists!";
        } else {
            // Update users table
            $user_query = "UPDATE users SET 
                          full_name = '$fullname', 
                          username = '$username', 
                          email = '$email' 
                          WHERE id = $student_user_id";
            
            if (mysqli_query($pdo, $user_query)) {
                // Update students table
                $student_update_query = "UPDATE students SET 
                                       student_id = '$student_id', 
                                       phone = '$phone', 
                                       address = '$address', 
                                       date_of_birth = '$date_of_birth', 
                                       gender = '$gender' 
                                       WHERE user_id = $student_user_id";
                
                if (mysqli_query($pdo, $student_update_query)) {
                    $message = "Student updated successfully!";
                    // Refresh student data
                    $student_result = mysqli_query($pdo, $student_query);
                    $student = mysqli_fetch_assoc($student_result);
                } else {
                    $message = "Error updating student details: " . mysqli_error($pdo);
                }
            } else {
                $message = "Error updating user: " . mysqli_error($pdo);
            }
        }
    } else {
        $message = "All required fields must be filled!";
    }
}

include 'includes/header.php';
?>

<div class="main-content">
    <h1>Edit Student</h1>
    
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="form-section">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Student ID *</label>
                    <input type="text" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <button type="submit" name="update_student" class="btn-primary">Update Student</button>
                <a href="manage_students.php" class="btn-edit" style="text-decoration: none; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>