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
    $html .= '</ul></nav>';
    return $html;
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
