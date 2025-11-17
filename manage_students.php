<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle Add Student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_student'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $gender = trim($_POST['gender']);

    if ($fullname && $username && $password && $student_id) {
        // Check if username exists
        $check = mysqli_query($pdo, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Username already exists!";
        } else {
            // Insert into users table
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $user_query = "INSERT INTO users (full_name, username, password, role, email) 
                          VALUES ('$fullname', '$username', '$hashed', 'student', '$email')";
            
            if (mysqli_query($pdo, $user_query)) {
                $user_id = mysqli_insert_id($pdo);
                
                // Insert into students table
                $student_query = "INSERT INTO students (user_id, student_id, phone, address, date_of_birth, gender) 
                                VALUES ($user_id, '$student_id', '$phone', '$address', '$date_of_birth', '$gender')";
                
                if (mysqli_query($pdo, $student_query)) {
                    $message = "Student added successfully!";
                } else {
                    $message = "Error adding student details: " . mysqli_error($pdo);
                }
            } else {
                $message = "Error creating user: " . mysqli_error($pdo);
            }
        }
    } else {
        $message = "All required fields must be filled!";
    }
}

// Handle Delete Student
if (isset($_GET['delete_student'])) {
    $user_id = $_GET['delete_student'];
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    if (mysqli_query($pdo, $delete_query)) {
        $message = "Student deleted successfully!";
    } else {
        $message = "Error deleting student: " . mysqli_error($pdo);
    }
}

// Fetch all students
$students_query = "
    SELECT u.id, u.full_name, u.username, u.email, s.student_id, s.phone, s.address, s.date_of_birth, s.gender
    FROM users u 
    JOIN students s ON u.id = s.user_id 
    WHERE u.role = 'student'
    ORDER BY u.full_name
";
$students_result = mysqli_query($pdo, $students_query);
$students = [];
if ($students_result && mysqli_num_rows($students_result) > 0) {
    $students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);
}

include 'includes/header.php';
?>

<div class="main-content">
    <h1>Manage Students</h1>
    
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Add Student Form -->
    <div class="form-section">
        <h2>Add New Student</h2>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="fullname" required>
                </div>
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Student ID *</label>
                    <input type="text" name="student_id" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="add_student" class="btn-primary">Add Student</button>
        </form>
    </div>

    <!-- Students List -->
    <div class="list-section">
        <h2>All Students</h2>
        <?php if (!empty($students)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['username']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                    <td><?php echo htmlspecialchars($student['gender']); ?></td>
                    <td>
                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn-edit">Edit</a>
                        <a href="manage_students.php?delete_student=<?php echo $student['id']; ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No students found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>