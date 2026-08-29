<?php
/**
 * TEMPORARY diagnostic for the cows page 500 error.
 * While logged in, visit: https://<domain>/backend/migrations/diag_cows.php
 * DELETE THIS FILE when done.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    http_response_code(403);
    exit('Forbidden — log in first.');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/plain; charset=utf-8');
echo "PHP version: " . PHP_VERSION . "\n";
echo "DB server: ";

$conn = getDatabase();
echo "{$conn->server_info}\n\n";

function step(string $label, callable $fn) {
    echo "--- $label ---\n";
    try {
        $r = $fn();
        echo "[OK]" . ($r !== null && $r !== '' ? " $r" : "") . "\n\n";
    } catch (Throwable $e) {
        echo "[FAIL] " . $e->getMessage() . "\n  at " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    }
}

$user_id = (int)$_SESSION['user_id'];

step("cows table columns", function() use ($conn) {
    $r = $conn->query("SHOW COLUMNS FROM cows");
    if (!$r) return $conn->error;
    $cols = [];
    while ($row = $r->fetch_assoc()) $cols[] = $row['Field'];
    return implode(', ', $cols);
});

step("Columns the cows page SELECTs", function() use ($conn) {
    $required = ['id','ear_tag','cow_name','breed','gender','date_of_birth','weight_kg','status','notes','image_path','created_at'];
    $r = $conn->query("SHOW COLUMNS FROM cows");
    $cols = [];
    while ($row = $r->fetch_assoc()) $cols[] = $row['Field'];
    $missing = array_diff($required, $cols);
    return $missing ? "MISSING: " . implode(', ', $missing) . "  <-- RUN THE ALTER BELOW" : "all present";
});

step("status enum values", function() use ($conn) {
    $r = $conn->query("SHOW COLUMNS FROM cows LIKE 'status'");
    $row = $r->fetch_assoc();
    return $row ? $row['Type'] : 'n/a';
});

step("farms lookup", function() use ($conn, $user_id) {
    $s = $conn->prepare("SELECT id FROM farms WHERE user_id = ? LIMIT 1");
    $s->bind_param("i", $user_id);
    $s->execute();
    $f = $s->get_result()->fetch_assoc();
    $s->close();
    return $f ? 'farm_id = ' . $f['id'] : 'NO FARM — backend would die() here';
});

step("Full cows SELECT (as the page runs it)", function() use ($conn, $user_id) {
    $s = $conn->prepare("
        SELECT id, ear_tag AS tag_number, cow_name AS name, breed, gender,
               date_of_birth AS birth_date, weight_kg, status, notes, image_path, created_at
        FROM cows WHERE user_id = ? ORDER BY created_at DESC
    ");
    $s->bind_param("i", $user_id);
    $s->execute();
    $n = $s->get_result()->num_rows;
    $s->close();
    return "$n cow(s)";
});

step("logs dir writable (upload_debug.log)", function() {
    $dir = __DIR__ . '/../../logs';
    if (!is_dir($dir)) return "logs/ dir DOES NOT EXIST — file_put_contents will fail on upload (warning, not 500)";
    return is_writable($dir) ? 'exists + writable' : 'exists but NOT writable';
});

step("public/uploads/cows dir writable", function() {
    $dir = __DIR__ . '/../../public/uploads/cows';
    if (!is_dir($dir)) return "MISSING — image uploads will fail (warning, not 500)";
    return is_writable($dir) ? 'exists + writable' : 'exists but NOT writable';
});

echo "=== END — any FAIL above is the cause of the 500 ===\n";