<?php
/**
 * AquaGuard - Cloud Database Initializer
 * Imports aquaguard_backup.sql into the connected MySQL database safely.
 */

require_once __DIR__ . '/config/database.php';

$isCli = (php_sapi_name() === 'cli');

function outputMsg($msg, $isCli) {
    if ($isCli) {
        echo $msg . "\n";
    } else {
        echo "<p style='font-family: monospace; font-size: 14px; margin: 4px 0;'>" . htmlspecialchars($msg) . "</p>";
    }
}

if (!$isCli) {
    echo "<!DOCTYPE html><html><head><title>Database Setup | AquaGuard</title>";
    echo "<style>body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; }</style>";
    echo "</head><body><div style='max-width: 650px; margin: 0 auto; background: #1e293b; padding: 24px; border-radius: 12px;'>";
    echo "<h2 style='color: #38bdf8; margin-top: 0;'>AquaGuard Database Setup</h2>";
}

outputMsg("Testing database connection...", $isCli);

if (!$conn || $conn->connect_error) {
    outputMsg("FAILED: Could not connect to MySQL database: " . ($conn ? $conn->connect_error : 'Unknown error'), $isCli);
    if (!$isCli) echo "</div></body></html>";
    exit(1);
}

outputMsg("Connected successfully to database: " . $dbname . " on " . $host, $isCli);

// Check if tables already exist
$check = $conn->query("SHOW TABLES LIKE 'users'");
if ($check && $check->num_rows > 0) {
    outputMsg("Database already initialized ('users' table exists).", $isCli);
    if (!$isCli) {
        echo "<br><a href='index.php' style='display: inline-block; background: #38bdf8; color: #0f172a; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;'>Go to AquaGuard Home</a>";
        echo "</div></body></html>";
    }
    exit(0);
}

$sqlFile = __DIR__ . '/aquaguard_backup.sql';
if (!file_exists($sqlFile)) {
    outputMsg("Error: aquaguard_backup.sql file not found.", $isCli);
    if (!$isCli) echo "</div></body></html>";
    exit(1);
}

outputMsg("Reading and parsing aquaguard_backup.sql...", $isCli);
$sqlContent = file_get_contents($sqlFile);

// Remove transaction wrappers
$sqlContent = preg_replace('/START\s+TRANSACTION\s*;/i', '', $sqlContent);
$sqlContent = preg_replace('/COMMIT\s*;/i', '', $sqlContent);

// Extract triggers (which contain internal semicolons and DELIMITER directives)
$triggers = [];
$cleanSql = preg_replace_callback('/DELIMITER\s+\$\$(.*?)DELIMITER\s+;/si', function($m) use (&$triggers) {
    $triggers[] = trim(str_replace('$$', '', $m[1]));
    return '';
}, $sqlContent);

// Strip any leftover delimiter lines
$cleanSql = preg_replace('/^\s*DELIMITER\s+.*$/m', '', $cleanSql);

outputMsg("Importing tables, views, and data...", $isCli);

if ($conn->multi_query($cleanSql)) {
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
}

if ($conn->errno) {
    outputMsg("Notice on table creation: " . $conn->error, $isCli);
} else {
    outputMsg("SUCCESS: Tables, views, and seed data imported!", $isCli);
}

if (!empty($triggers)) {
    outputMsg("Creating triggers...", $isCli);
    foreach ($triggers as $trg) {
        if (!empty(trim($trg))) {
            $conn->query($trg);
        }
    }
    outputMsg("SUCCESS: Triggers registered!", $isCli);
}

// Final verification
$verify = $conn->query("SHOW TABLES LIKE 'users'");
if ($verify && $verify->num_rows > 0) {
    outputMsg("ALL DONE! 'users' table is verified.", $isCli);
} else {
    outputMsg("WARNING: 'users' table not found after import. Please check table definitions.", $isCli);
}

if (!$isCli) {
    echo "<br><a href='index.php' style='display: inline-block; background: #38bdf8; color: #0f172a; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;'>Go to AquaGuard Home</a>";
    echo "</div></body></html>";
}
?>
