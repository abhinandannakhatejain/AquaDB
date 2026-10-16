<?php

// Check for URL-based connection string (common in Railway/Render/Heroku)
$databaseUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');

if ($databaseUrl && strpos($databaseUrl, 'mysql') === 0) {
    $parsed = parse_url($databaseUrl);
    $host = $parsed['host'] ?? 'localhost';
    $port = intval($parsed['port'] ?? 3306);
    $username = $parsed['user'] ?? 'root';
    $password = $parsed['pass'] ?? '';
    $dbname = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'aquaguard';
} else {
    $host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost';
    $port = intval(getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);
    $dbname = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'aquaguard';
    $username = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: '';
}

// 1. PDO Connection
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    // We allow deferred connection failure handling if $conn is used or for graceful errors
    $pdo = null;
    $pdo_error = $e->getMessage();
}

// 2. MySQLi Connection
$conn = @new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    // If both failed and we are running a page that needs database:
    if ($pdo === null) {
        die("Database connection failed: " . $conn->connect_error . " (PDO error: " . ($pdo_error ?? 'none') . ")");
    }
} else {
    $conn->set_charset("utf8mb4");
}
?>