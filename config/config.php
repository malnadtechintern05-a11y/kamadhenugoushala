<?php
/**
 * Application Configuration — Kamadhenu Goushala
 */

// ─── Base URL ────────────────────────────────────────────────────────────────
// Change this to your domain when deploying to production.
// For XAMPP local: 'http://localhost/kamadhenugoushala'
define('BASE_URL', 'http://localhost/kamadhenugoushala');

// ─── Site Info ────────────────────────────────────────────────────────────────
define('SITE_NAME',    'Kamadhenu Goushala');
define('SITE_TAGLINE', 'Serving the Sacred Cow Since 1998');
define('SITE_EMAIL',   'info@kamadhenugoushala.org');
define('SITE_PHONE',   '+91 98765 43210');
define('SITE_ADDRESS', 'Village Dharmapur, Dist. Mysuru, Karnataka — 571 201');
define('SOCIAL_FACEBOOK', '#');
define('SOCIAL_INSTAGRAM', '#');
define('SOCIAL_TWITTER', '#');
define('SOCIAL_WHATSAPP', '#');
// ─── Upload Paths ─────────────────────────────────────────────────────────────
define('UPLOAD_DIR',          __DIR__ . '/../uploads/');
define('UPLOAD_COWS_DIR',     UPLOAD_DIR . 'cows/');
define('UPLOAD_PRODUCTS_DIR', UPLOAD_DIR . 'products/');
define('UPLOAD_GALLERY_DIR',  UPLOAD_DIR . 'gallery/');
define('UPLOAD_EVENTS_DIR',   UPLOAD_DIR . 'events/');

define('UPLOAD_URL',          BASE_URL . '/uploads/');
define('UPLOAD_COWS_URL',     UPLOAD_URL . 'cows/');
define('UPLOAD_PRODUCTS_URL', UPLOAD_URL . 'products/');
define('UPLOAD_GALLERY_URL',  UPLOAD_URL . 'gallery/');
define('UPLOAD_EVENTS_URL',   UPLOAD_URL . 'events/');

// ─── Upload Limits ────────────────────────────────────────────────────────────
define('UPLOAD_MAX_SIZE',  5 * 1024 * 1024); // 5 MB
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// ─── Pagination ───────────────────────────────────────────────────────────────
define('ITEMS_PER_PAGE', 12);

// ─── Session ──────────────────────────────────────────────────────────────────
define('SESSION_NAME', 'kg_session');

// ─── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set('Asia/Kolkata');

// ─── Placeholder Image ────────────────────────────────────────────────────────
define('PLACEHOLDER_IMG', BASE_URL . '/assets/images/placeholder.jpg');

// ─── Checkout Modes ───────────────────────────────────────────────────────────
define('CHECKOUT_MODE_COWS', 'whatsapp');
define('CHECKOUT_MODE_PRODUCTS', 'both');
define('WA_DEFAULT_MSG_COWS', '');
define('WA_DEFAULT_MSG_PRODUCTS', '');
define('WA_PHONE_COWS', '');
define('WA_PHONE_PRODUCTS', '');
