<?php
// includes/config.example.php
// Copy this file to config.php and adjust credentials as needed

// Docker defaults (use these for Docker setup)
$host = 'db';
$db   = 'khoj_db';
$user = 'root';
$pass = 'khoj_root_pass';

// For local XAMPP development, use these instead:
// $host = 'localhost';
// $db   = 'Khoj_db';
// $user = 'root';
// $pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
