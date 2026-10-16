<?php

// Helper to read env vars across getenv, $_ENV, $_SERVER, and container boot file
function getDbEnv($key, $default = '') {
    $val = getenv($key);
    if ($val !== false && $val !== '') return $val;
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];

    static $cloudEnv = null;
    if ($cloudEnv === null) {
        $envFile = __DIR__ . '/.cloud_env';
        $cloudEnv = file_exists($envFile) ? @parse_ini_file($envFile) : [];
    }
    if (is_array($cloudEnv) && isset($cloudEnv[$key]) && $cloudEnv[$key] !== '') {
        return $cloudEnv[$key];
    }

    return $default;
}

$databaseUrl = getDbEnv('MYSQL_URL', getDbEnv('DATABASE_URL'));
$databaseUrl = trim($databaseUrl, "\"' \t\n\r\0\x0B");

if ($databaseUrl && strpos($databaseUrl, '${{') === 0) {
    die("Database configuration error: Railway variable reference <code>" . htmlspecialchars($databaseUrl) . "</code> was not resolved by Railway.<br><br><strong>Fix:</strong> In Railway, open your <strong>MySQL</strong> service &rarr; click <strong>Variables</strong> &rarr; copy the actual value of <strong>MYSQL_URL</strong> (starts with <code>mysql://...</code>) and paste it into your AquaDB service's <strong>MYSQL_URL</strong>.");
}

if ($databaseUrl && (strpos($databaseUrl, 'mysql') === 0 || strpos($databaseUrl, '://') !== false)) {
    $parsed = parse_url($databaseUrl);
    if (!empty($parsed['host'])) {
        $host = $parsed['host'];
        $port = intval($parsed['port'] ?? 3306);
        $username = $parsed['user'] ?? 'root';
        $password = isset($parsed['pass']) ? urldecode($parsed['pass']) : '';
        $dbname = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'aquaguard';
    }
}

if (!isset($host)) {
    $host = getDbEnv('MYSQLHOST', getDbEnv('DB_HOST', '127.0.0.1'));
    $port = intval(getDbEnv('MYSQLPORT', getDbEnv('DB_PORT', 3306)));
    $dbname = getDbEnv('MYSQLDATABASE', getDbEnv('DB_NAME', 'aquaguard'));
    $username = getDbEnv('MYSQLUSER', getDbEnv('DB_USER', 'root'));
    $password = getDbEnv('MYSQLPASSWORD', getDbEnv('DB_PASSWORD', getDbEnv('DB_PASS', '')));
}

// 1. PDO Connection
$pdo = null;
$pdo_error = null;
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
    $pdo_error = $e->getMessage();
}

// 2. MySQLi Connection
mysqli_report(MYSQLI_REPORT_OFF);
try {
    $conn = @new mysqli($host, $username, $password, $dbname, $port);
} catch (Throwable $e) {
    $conn = null;
    $mysqli_error = $e->getMessage();
}

if (!$conn || $conn->connect_error) {
    $err = $conn ? $conn->connect_error : ($mysqli_error ?? 'Unknown error');
    $hint = ($host === '127.0.0.1' || $host === 'localhost')
        ? "<br><strong>Hint:</strong> Cloud database variables are missing. In Railway/Render, add a MySQL service and link it to your web service (e.g. set <code>MYSQL_URL</code> or <code>MYSQLHOST</code>)."
        : "";
    die("Database connection failed: " . htmlspecialchars($err) . $hint);
} else {
    $conn->set_charset("utf8mb4");
}
?>