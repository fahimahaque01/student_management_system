<?php
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = "Please fill all fields!";
    } else {
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($pdo, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Only use password_verify for secure password checking
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($user['role'] === 'teacher') {
                    header("Location: teacher_dashboard.php");
                } elseif ($user['role'] === 'student') {
                    header("Location: student_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $message = "Invalid username or password!";
            }
        } else {
            $message = "Invalid username or password!";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Login</h2>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
        <label>Username:</label>
        <input type="text" name="username" placeholder="Enter username" autocomplete="off" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password" autocomplete="new-password" required>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>

    <!-- Demo Accounts Info -->
    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4>Demo Accounts:</h4>
        <p><strong>Admin:</strong> admin</p>
        <p><strong>Teacher:</strong> teacher</p>
        <p><strong>Student:</strong> imran</p>
        <p><strong>Password for all:</strong> 123456</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>