<?php
/**
 * Language Switcher — Kamadhenu Goushala
 * Sets the Google Translate cookie (googtrans) server-side via PHP
 * so it works reliably on any hosting, including free hosts.
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config/config.php';
}

$allowed = ['en', 'hi', 'kn'];
$lang    = $_POST['lang'] ?? $_GET['lang'] ?? 'en';
if (!in_array($lang, $allowed, true)) {
    $lang = 'en';
}

// Figure out where to redirect back to (must be on the same host for safety)
$redirect  = $_POST['redirect'] ?? '';
$currentHost = $_SERVER['HTTP_HOST'] ?? '';
$redirectHost = parse_url($redirect, PHP_URL_HOST) ?? '';

// Only allow redirects to the same host; fall back to home page otherwise
if (empty($redirect) || $redirectHost !== $currentHost) {
    $redirect = BASE_URL . '/';
}

$host = $_SERVER['HTTP_HOST'] ?? parse_url(BASE_URL, PHP_URL_HOST);

if ($lang === 'en') {
    // Clear Google Translate cookies
    setcookie('googtrans', '', time() - 3600, '/', $host);
    setcookie('googtrans', '', time() - 3600, '/', '.' . $host);
} else {
    $value   = '/en/' . $lang;
    $expires = time() + 86400; // 1 day
    setcookie('googtrans', $value, $expires, '/', $host);
    setcookie('googtrans', $value, $expires, '/', '.' . $host);
}

header('Location: ' . $redirect);
exit;
