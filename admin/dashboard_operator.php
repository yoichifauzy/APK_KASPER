<?php
require_once __DIR__ . '/../config/cek_login.php';
// Only admins should be redirected to admin dashboard
otorisasi(['admin']);

// Preserve query string (e.g. pie_month, pie_year) if present
$query = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? ('?' . $_SERVER['QUERY_STRING']) : '';

auth_redirect:
// Redirect to the admin dashboard
header('Location: dashboard_admin.php' . $query);
exit;
