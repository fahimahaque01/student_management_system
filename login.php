<?php
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username && $password) {
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($pdo, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Debug: Check what's happening
            // echo "DB Password: " . $user['password'] . "<br>";
            // echo "Input Password: " . $password . "<br>";
            
            // Check if password matches plain text OR hashed
            if ($user['password'] === $password || password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit;
            } else {
                $message = "Invalid password!";
            }
        } else {
            $message = "Username not found!";
        }
    } else {
        $message = "Please fill all fields!";
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
        <p><strong>Admin:</strong> admin / 123456</p>
        <p><strong>Teacher:</strong> teacher / 123456</p>
        <p><strong>Student:</strong> fahima / 123456</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>