<?php
/**
 * HTML Head / Header — Kamadhenu Goushala
 * @var string $pageTitle   - Set in each page before including this file
 * @var string $pageDesc    - Meta description (optional)
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}
$pageTitle = $pageTitle ?? SITE_NAME;
$pageDesc  = $pageDesc  ?? 'Kamadhenu Goushala — A sacred sanctuary dedicated to the protection and care of indigenous Indian cows (Gau Mata). Adopt a cow, donate, and support Gau Seva.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($pageDesc) ?>">
  <meta name="theme-color" content="#F4F1EA">
  <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>

  <!-- Favicon -->
  <link rel="icon" href="<?= BASE_URL ?>/assets/images/favicon.svg?v=3" type="image/svg+xml">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Noto+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
