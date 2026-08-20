<?php
/**
 * Authentication Helpers — Kamadhenu Goushala
 */

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

/**
 * Check if the admin is logged in.
 */
function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_logged_in']);
}

/**
 * Require admin authentication. Redirects to login if not authenticated.
 */
function require_admin_auth(): void {
    if (!is_admin_logged_in()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Log in the admin: store session data and regenerate session ID.
 */
function admin_login(int $adminId, string $username): void {
    session_regenerate_id(true);
    $_SESSION['admin_id']       = $adminId;
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_logged_in'] = true;
}

/**
 * Log out the admin: destroy the session completely.
 */
function admin_logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Get the currently logged-in admin's username.
 */
function current_admin_username(): string {
    return $_SESSION['admin_username'] ?? 'Admin';
}
