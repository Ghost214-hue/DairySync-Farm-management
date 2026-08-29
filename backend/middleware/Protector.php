<?php
/**
 * Global Authentication Protector
 * Include this at the top of any protected page
 */

require_once __DIR__ . '/Auth.php';

// Require authentication - redirects to signin if not logged in
$user = Auth::requireAuth();

// Additional authorization checks can be added here
// e.g., check user roles, permissions, farm ownership, etc.