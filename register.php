<?php
require_once 'config.php';


$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if ($fullname && $username && $password && $role) {
        $check = mysqli_query($pdo, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Username already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (full_name, username, password, role) VALUES ('$fullname', '$username', '$hashed', '$role')";
            if (mysqli_query($pdo, $query)) {
                $message = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $message = "Error: " . mysqli_error($pdo);
            }
        }
    } else {
        $message = "All fields are required!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Register</h2>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
        <label>Full Name:</label>
        <input type="text" name="fullname" placeholder="Enter full name" autocomplete="off" required>

        <label>Username:</label>
        <input type="text" name="username" placeholder="Enter username" autocomplete="off" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password" autocomplete="new-password" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="" disabled selected>Select Role</option>
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
</div>

<?php include 'includes/footer.php'; ?>
