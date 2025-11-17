<?php
session_start();

// Database configuration
$host = "localhost";
$user = "root";
$password = ""; // Ei line e password thik koren
$dbname = "student_management";

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
$pdo = $conn;
?>