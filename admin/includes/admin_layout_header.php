<?php
/**
 * Admin Layout Helper — outputs the admin sidebar + topbar shell
 * Usage: require_once at the top of each admin page AFTER setting:
 *   $adminPageTitle (string)
 *   $adminActivePage (string)
 */
$adminPageTitle  = $adminPageTitle  ?? 'Dashboard';
$adminActivePage = $adminActivePage ?? '';

function admin_sidebar_link(string $href, string $icon, string $label, string $active, string $key): string {
    $cls = ($active === $key) ? ' active' : '';
    return <<<HTML
<a href="{$href}" class="kg-sidebar-link{$cls}">
  <i class="bi bi-{$icon}"></i>
  {$label}
</a>
HTML;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($adminPageTitle) ?> | Admin | <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Noto+Serif:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="kg-admin-body">

<!-- Sidebar -->
<aside class="kg-sidebar" id="adminSidebar">
  <div class="kg-sidebar-brand">
    <div class="kg-logo-icon-sm"><i class="bi bi-flower2"></i></div>
    <div class="kg-sidebar-brand-text">
      <span class="kg-sidebar-brand-main">Kamadhenu</span>
      <span class="kg-sidebar-brand-sub">Admin Panel</span>
    </div>
  </div>

  <nav class="kg-sidebar-nav">
    <div class="kg-sidebar-nav-label">Overview</div>
    <?= admin_sidebar_link(BASE_URL . '/admin/dashboard.php', 'speedometer2', 'Dashboard', $adminActivePage, 'dashboard') ?>

    <div class="kg-sidebar-nav-label mt-2">Manage</div>
    <?= admin_sidebar_link(BASE_URL . '/admin/cows.php',     'emoji-heart-eyes', 'Cows',       $adminActivePage, 'cows') ?>
    <?= admin_sidebar_link(BASE_URL . '/admin/products.php', 'shop',             'Products',   $adminActivePage, 'products') ?>
    <?= admin_sidebar_link(BASE_URL . '/admin/gallery.php',  'images',           'Gallery',    $adminActivePage, 'gallery') ?>
    <?= admin_sidebar_link(BASE_URL . '/admin/events.php',   'calendar-event',   'Events',     $adminActivePage, 'events') ?>

    <div class="kg-sidebar-nav-label mt-2">Records</div>
    <?= admin_sidebar_link(BASE_URL . '/admin/donations.php',  'currency-rupee', 'Donations',  $adminActivePage, 'donations') ?>
    <?= admin_sidebar_link(BASE_URL . '/admin/adoptions.php',  'heart-fill',     'Adoptions',  $adminActivePage, 'adoptions') ?>
    <?= admin_sidebar_link(BASE_URL . '/admin/messages.php',   'envelope-fill',  'Messages',   $adminActivePage, 'messages') ?>
    <?= admin_sidebar_link(BASE_URL . '/admin/volunteers.php', 'people-fill',    'Volunteers', $adminActivePage, 'volunteers') ?>

    <div class="kg-sidebar-nav-label mt-2">Settings</div>
    <?= admin_sidebar_link(BASE_URL . '/admin/settings.php', 'gear-fill', 'Settings', $adminActivePage, 'settings') ?>
  </nav>

  <div class="kg-sidebar-footer">
    <a href="<?= BASE_URL ?>/" class="kg-sidebar-link" target="_blank">
      <i class="bi bi-box-arrow-up-right"></i> View Website
    </a>
    <a href="<?= BASE_URL ?>/admin/logout.php" class="kg-sidebar-link mt-1">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</aside>

<!-- Main Content Wrapper -->
<main class="kg-admin-main">
  <!-- Topbar -->
  <div class="kg-admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn d-lg-none" style="color:var(--kg-green);" aria-label="Toggle sidebar">
        <i class="bi bi-list fs-4"></i>
      </button>
      <h5 class="mb-0"><?= e($adminPageTitle) ?></h5>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.85rem;color:var(--kg-text-muted);">
        <i class="bi bi-person-circle me-1"></i><?= e(current_admin_username()) ?>
      </span>
      <a href="<?= BASE_URL ?>/admin/logout.php" class="btn btn-sm" style="background:#fee2e2;color:#b91c1c;border-radius:8px;">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>

  <!-- Page Content -->
  <div class="kg-admin-content">
