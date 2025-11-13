<?php
session_start();

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_management";

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Optional: set UTF-8 encoding for Bangla or special characters
mysqli_set_charset($conn, "utf8");

// Optional: keep a variable named $pdo for compatibility with existing files
$pdo = $conn;
?>
