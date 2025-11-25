<?php
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $email = trim($_POST['email']);
    $student_id = trim($_POST['student_id']);

    // Validation
    if (empty($fullname) || empty($username) || empty($password) || empty($role)) {
        $message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Valid email is required!";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long!";
    } elseif ($role === 'student' && empty($student_id)) {
        $message = "Student ID is required for student registration!";
    } else {
        // Check if username already exists
        $check_username = mysqli_query($pdo, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($check_username) > 0) {
            $message = "Username already exists!";
        } else {
            // Check if student_id already exists for students
            if ($role === 'student') {
                $check_student = mysqli_query($pdo, "SELECT * FROM students WHERE student_id='$student_id'");
                if (mysqli_num_rows($check_student) > 0) {
                    $message = "Student ID already exists! Please use a different Student ID.";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $query = "INSERT INTO users (full_name, username, password, role, email) VALUES ('$fullname', '$username', '$hashed', '$role', '$email')";
                    
                    if (mysqli_query($pdo, $query)) {
                        $user_id = mysqli_insert_id($pdo);
                        
                        // Create record in students table
                        $student_query = "INSERT INTO students (user_id, student_id) VALUES ($user_id, '$student_id')";
                        if (mysqli_query($pdo, $student_query)) {
                            $message = "Registration successful! Student account created. You can now <a href='login.php'>login</a>.";
                        } else {
                            $message = "User created but student record failed: " . mysqli_error($pdo);
                        }
                    } else {
                        $message = "Error: " . mysqli_error($pdo);
                    }
                }
            } else {
                // For admin and teacher roles
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (full_name, username, password, role, email) VALUES ('$fullname', '$username', '$hashed', '$role', '$email')";
                
                if (mysqli_query($pdo, $query)) {
                    $message = "Registration successful! You can now <a href='login.php'>login</a>.";
                } else {
                    $message = "Error: " . mysqli_error($pdo);
                }
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Register</h2>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off" id="registrationForm">
        <label>Full Name:*</label>
        <input type="text" name="fullname" placeholder="Enter full name" autocomplete="off" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">

        <label>Username:*</label>
        <input type="text" name="username" placeholder="Enter username" autocomplete="off" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

        <label>Email:*</label>
        <input type="email" name="email" placeholder="Enter email address" autocomplete="off" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label>Password:*</label>
        <input type="password" name="password" placeholder="Enter password (min. 6 characters)" autocomplete="new-password" required minlength="6">

        <label>Role:*</label>
        <select name="role" id="roleSelect" required>
            <option value="" disabled selected>Select Role</option>
            <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="teacher" <?php echo (isset($_POST['role']) && $_POST['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
            <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
        </select>

        <!-- Student ID field (only shown for student role) -->
        <div id="studentIdField" style="display: none;">
            <label>Student ID:*</label>
            <input type="text" name="student_id" placeholder="Enter your Student ID" value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
            <small style="color: #666; font-size: 0.9em;">Enter your official Student ID provided by your institution</small>
        </div>

        <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    var studentIdField = document.getElementById('studentIdField');
    var studentIdInput = studentIdField.querySelector('input');
    
    if (this.value === 'student') {
        studentIdField.style.display = 'block';
        studentIdInput.required = true;
    } else {
        studentIdField.style.display = 'none';
        studentIdInput.required = false;
    }
});

// Show student ID field if student role is already selected (on page reload)
document.addEventListener('DOMContentLoaded', function() {
    var roleSelect = document.getElementById('roleSelect');
    if (roleSelect.value === 'student') {
        document.getElementById('studentIdField').style.display = 'block';
        document.getElementById('studentIdField').querySelector('input').required = true;
    }
});

// Email validation
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    var email = document.querySelector('input[name="email"]').value;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address!');
        e.preventDefault();
        return false;
    }
});
</script>

<style>
.form-container {
    max-width: 500px;
    margin: 20px auto;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-container label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-container input,
.form-container select {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
}

.form-container input:focus,
.form-container select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.form-container button {
    width: 100%;
    padding: 12px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
}

.form-container button:hover {
    background: #0056b3;
}

.message {
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
}

.message:not(:empty) {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #495057;
}

small {
    display: block;
    margin-top: 5px;
}
</style>

<?php include 'includes/footer.php'; ?>