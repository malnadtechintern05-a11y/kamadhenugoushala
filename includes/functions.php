<?php
/**
 * General Helper Functions — Kamadhenu Goushala
 */

/**
 * Escape output for HTML context.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirect to a URL.
 */
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

/**
 * Set a flash message in the session.
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Retrieve and clear a flash message.
 */
function get_flash(string $type): string {
    $msg = $_SESSION['flash'][$type] ?? '';
    unset($_SESSION['flash'][$type]);
    return $msg;
}

/**
 * Check if a flash message exists.
 */
function has_flash(string $type): bool {
    return !empty($_SESSION['flash'][$type]);
}

/**
 * Display Bootstrap 5 flash alert.
 */
function flash_alert(): string {
    $html = '';
    foreach (['success', 'danger', 'warning', 'info'] as $type) {
        if (has_flash($type)) {
            $msg   = e(get_flash($type));
            $icon  = match($type) {
                'success' => 'check-circle-fill',
                'danger'  => 'exclamation-triangle-fill',
                'warning' => 'exclamation-triangle-fill',
                'info'    => 'info-circle-fill',
            };
            $html .= <<<HTML
<div class="alert alert-{$type} alert-dismissible fade show d-flex align-items-center" role="alert">
  <i class="bi bi-{$icon} me-2"></i>
  <div>{$msg}</div>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;
        }
    }
    return $html;
}

/**
 * Upload an image file and return the filename on success.
 * Throws RuntimeException on failure.
 */
function upload_image(array $file, string $destDir): string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload error code: ' . $file['error']);
    }
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new RuntimeException('File exceeds maximum allowed size of 5 MB.');
    }

    // Validate MIME via finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_MIME_TYPES, true)) {
        throw new RuntimeException('Invalid file type. Only JPEG, PNG, WebP, GIF are allowed.');
    }

    // Validate extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
        throw new RuntimeException('Invalid file extension.');
    }

    // Generate unique filename
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destPath = rtrim($destDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }
    return $filename;
}

/**
 * Delete an uploaded image file safely.
 */
function delete_image(string $dir, string $filename): void {
    if (empty($filename)) return;
    $path = rtrim($dir, '/') . '/' . basename($filename);
    if (file_exists($path) && is_file($path)) {
        @unlink($path);
    }
}

/**
 * Return an image URL — uploads/ or placeholder if empty/missing.
 */
function img_url(string $subdir, ?string $filename): string {
    if (empty($filename)) return PLACEHOLDER_IMG;
    $path = UPLOAD_DIR . $subdir . '/' . $filename;
    if (!file_exists($path)) return PLACEHOLDER_IMG;
    return UPLOAD_URL . $subdir . '/' . e($filename);
}

/**
 * Paginate a query: return ['items' => [...], 'total_pages' => N, 'current_page' => N]
 */
function paginate(PDO $pdo, string $countSql, string $dataSql, array $params, int $page, int $perPage = ITEMS_PER_PAGE): array {
    $page    = max(1, (int)$page);
    $offset  = ($page - 1) * $perPage;

    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

    $stmtData = $pdo->prepare($dataSql . " LIMIT :limit OFFSET :offset");
    foreach ($params as $key => $val) {
        $stmtData->bindValue($key, $val);
    }
    $stmtData->bindValue(':limit',  $perPage, PDO::PARAM_INT);
    $stmtData->bindValue(':offset', $offset,  PDO::PARAM_INT);
    $stmtData->execute();
    $items = $stmtData->fetchAll();

    return [
        'items'        => $items,
        'total'        => $total,
        'total_pages'  => (int)ceil($total / $perPage),
        'current_page' => $page,
        'per_page'     => $perPage,
    ];
}

/**
 * Format currency in Indian Rupees.
 */
function format_inr(float $amount): string {
    return '₹' . number_format($amount, 2);
}

/**
 * Format a date string to a human-readable format.
 */
function format_date(string $dateStr): string {
    if (empty($dateStr)) return '—';
    return date('d M Y', strtotime($dateStr));
}

/**
 * Format a datetime string.
 */
function format_datetime(string $dateStr): string {
    if (empty($dateStr)) return '—';
    return date('d M Y, h:i A', strtotime($dateStr));
}

/**
 * Generate a Bootstrap 5 pagination HTML block.
 */
function pagination_html(array $pagData, string $baseUrl): string {
    if ($pagData['total_pages'] <= 1) return '';
    $cur   = $pagData['current_page'];
    $total = $pagData['total_pages'];
    $sep   = str_contains($baseUrl, '?') ? '&' : '?';
    $html  = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center flex-wrap gap-1">';
    $html .= '<li class="page-item' . ($cur <= 1 ? ' disabled' : '') . '">';
    $html .= '<a class="page-link" href="' . $baseUrl . $sep . 'page=' . ($cur - 1) . '">‹ Prev</a></li>';
    for ($i = 1; $i <= $total; $i++) {
        $html .= '<li class="page-item' . ($i === $cur ? ' active' : '') . '">';
        $html .= '<a class="page-link" href="' . $baseUrl . $sep . 'page=' . $i . '">' . $i . '</a></li>';
    }
    $html .= '<li class="page-item' . ($cur >= $total ? ' disabled' : '') . '">';
    $html .= '<a class="page-link" href="' . $baseUrl . $sep . 'page=' . ($cur + 1) . '">Next ›</a></li>';
    return $html . '</ul></nav>';
}

function get_cow_logo_svg() {
    if (defined('SITE_LOGO') && !empty(SITE_LOGO)) {
        $logoUrl = BASE_URL . '/uploads/branding/' . SITE_LOGO;
        return '<img src="' . htmlspecialchars($logoUrl) . '" alt="Logo" style="width: 2.2em; height: 2.2em; vertical-align: middle; object-fit: contain; margin-top: -2px;">';
    }
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="2em" height="2em" style="vertical-align: middle; margin-top: -2px;"><path d="M10.5,18A0.5,0.5 0 0,1 11,18.5A0.5,0.5 0 0,1 10.5,19A0.5,0.5 0 0,1 10,18.5A0.5,0.5 0 0,1 10.5,18M13.5,18A0.5,0.5 0 0,1 14,18.5A0.5,0.5 0 0,1 13.5,19A0.5,0.5 0 0,1 13,18.5A0.5,0.5 0 0,1 13.5,18M10,11A1,1 0 0,1 11,12A1,1 0 0,1 10,13A1,1 0 0,1 9,12A1,1 0 0,1 10,11M14,11A1,1 0 0,1 15,12A1,1 0 0,1 14,13A1,1 0 0,1 13,12A1,1 0 0,1 14,11M18,18C18,20.21 15.31,22 12,22C8.69,22 6,20.21 6,18C6,17.1 6.45,16.27 7.2,15.6C6.45,14.6 6,13.35 6,12L6.12,10.78C5.58,10.93 4.93,10.93 4.4,10.78C3.38,10.5 1.84,9.35 2.07,8.55C2.3,7.75 4.21,7.6 5.23,7.9C5.82,8.07 6.45,8.5 6.82,8.96L7.39,8.15C6.79,7.05 7,4 10,3L9.91,3.14V3.14C9.63,3.58 8.91,4.97 9.67,6.47C10.39,6.17 11.17,6 12,6C12.83,6 13.61,6.17 14.33,6.47C15.09,4.97 14.37,3.58 14.09,3.14L14,3C17,4 17.21,7.05 16.61,8.15L17.18,8.96C17.55,8.5 18.18,8.07 18.77,7.9C19.79,7.6 21.7,7.75 21.93,8.55C22.16,9.35 20.62,10.5 19.6,10.78C19.07,10.93 18.42,10.93 17.88,10.78L18,12C18,13.35 17.55,14.6 16.8,15.6C17.55,16.27 18,17.1 18,18M12,16C9.79,16 8,16.9 8,18C8,19.1 9.79,20 12,20C14.21,20 16,19.1 16,18C16,16.9 14.21,16 12,16M12,14C13.12,14 14.17,14.21 15.07,14.56C15.65,13.87 16,13 16,12A4,4 0 0,0 12,8A4,4 0 0,0 8,12C8,13 8.35,13.87 8.93,14.56C9.83,14.21 10.88,14 12,14M14.09,3.14V3.14Z"/></svg>';
}

/**
 * Get Site Favicon URL
 */
function get_site_favicon_url() {
    if (defined('SITE_FAVICON') && !empty(SITE_FAVICON)) {
        return BASE_URL . '/uploads/branding/' . SITE_FAVICON;
    }
    return BASE_URL . '/assets/images/favicon.svg';
}

function get_site_favicon_type() {
    if (defined('SITE_FAVICON') && !empty(SITE_FAVICON)) {
        $ext = strtolower(pathinfo(SITE_FAVICON, PATHINFO_EXTENSION));
        if ($ext === 'ico') return 'image/x-icon';
        if ($ext === 'png') return 'image/png';
        if ($ext === 'svg') return 'image/svg+xml';
    }
    return 'image/svg+xml';
}

/**
 * Sanitize a string for safe output / storage.
 */
function sanitize(string $input): string {
    return trim(strip_tags($input));
}

/**
 * Validate email address.
 */
function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate a positive number.
 */
function is_valid_amount(string $amount): bool {
    return is_numeric($amount) && (float)$amount > 0;
}

/**
 * Cart Functions
 */

function init_cart(): void {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function add_to_cart(int $product_id, int $quantity): void {
    init_cart();
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function update_cart(int $product_id, int $quantity): void {
    init_cart();
    if ($quantity <= 0) {
        remove_from_cart($product_id);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function remove_from_cart(int $product_id): void {
    init_cart();
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function get_cart(): array {
    init_cart();
    return $_SESSION['cart'];
}

function get_cart_count(): int {
    init_cart();
    return array_sum($_SESSION['cart']);
}

function clear_cart(): void {
    $_SESSION['cart'] = [];
}

