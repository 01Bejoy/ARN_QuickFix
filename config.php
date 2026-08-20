<?php
// Start session once (safe to include everywhere)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration (FORCED LOCAL NUMERICAL DIRECT GATEWAY IP FOR MAX SPEED)
$DB_HOST = "localhost";  
$DB_NAME = "arn_quickfix"; // Database name
$DB_USER = "root";
$DB_PASS = "";           // XAMPP default (empty)

// Data Source Name
$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // ❌ Error message
    die("❌ Database connection failed: " . $e->getMessage());
}
