<?php
// Create this as session_config.php in your project root directory

// Common session configuration - include this at the top of ALL files
// BEFORE any output is sent to browser

// Define project path consistently (adjust to match your server setup)
$project_base_path = '/'; // Use '/' if your site is in the root directory

// Set up session parameters
session_set_cookie_params([
    'lifetime' => 0, // 0 = until browser is closed
    'path' => $project_base_path,
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculate base URL consistently
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base_path = str_replace('\\', '/', dirname(__FILE__));
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$relative_path = str_replace($doc_root, '', $base_path);
$base_url = $protocol . $host . $relative_path . '/';
$base_url = rtrim($base_url, '/') . '/';

// Always set base_url in session for consistency
$_SESSION['base_url'] = $base_url;

// Debug function - can be removed in production
function debug_session() {
    echo "<div style='background:#f8f9fa;padding:10px;margin:10px;border:1px solid #ddd;'>";
    echo "<strong>Debug Session:</strong>";
    echo "<br>User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "Not set");
    echo "<br>User Type: " . (isset($_SESSION['user_type']) ? $_SESSION['user_type'] : "Not set");
    echo "<br>User Name: " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Not set");
    echo "<br>Base URL: " . (isset($_SESSION['base_url']) ? $_SESSION['base_url'] : "Not set");
    echo "</div>";
}
?>
