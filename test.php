<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>AquaDB Diagnostics</title>";
echo "<style>body { font-family: system-ui, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; }";
echo "code { background: #1e293b; padding: 2px 6px; border-radius: 4px; color: #38bdf8; } ul { line-height: 1.8; }</style></head><body>";
echo "<div style='max-width: 650px; margin: 0 auto; background: #1e293b; padding: 28px; border-radius: 12px;'>";
echo "<h2 style='color: #38bdf8; margin-top: 0;'>AquaDB Connection Diagnostics</h2>";

$envKeys = ['MYSQL_URL', 'DATABASE_URL', 'MYSQLHOST', 'MYSQLPORT', 'MYSQLUSER', 'MYSQLDATABASE'];
echo "<p><strong>Environment Variables Detected:</strong></p><ul>";
foreach ($envKeys as $key) {
    $val = getenv($key);
    if ($val === false || $val === '') $val = $_ENV[$key] ?? ($_SERVER[$key] ?? null);
    if ($val !== null && $val !== '') {
        $masked = (strlen($val) > 28) ? substr($val, 0, 14) . '...' . substr($val, -8) : $val;
        echo "<li><strong>$key</strong>: <code>" . htmlspecialchars($masked) . "</code></li>";
    } else {
        echo "<li><strong>$key</strong>: <em style='color: #94a3b8;'>not set</em></li>";
    }
}
echo "</ul><hr style='border: 1px solid #334155; margin: 20px 0;'>";

require_once __DIR__ . "/config/database.php";

echo "<h2 style='color: #4ade80;'>✅ AquaGuard Database Connected Successfully!</h2>";
echo "<p>Connected to database <code>" . htmlspecialchars($dbname) . "</code> on <code>" . htmlspecialchars($host) . "</code>.</p>";
echo "<p><a href='init_db.php' style='display: inline-block; background: #38bdf8; color: #0f172a; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;'>Run init_db.php Now</a></p>";
echo "</div></body></html>";
?>