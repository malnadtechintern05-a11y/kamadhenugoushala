<?php
/**
 * Navigation Bar — Kamadhenu Goushala
 * @var string $activePage - Set in each page: 'home','about','cows','seva','adopt','donate','products','gallery','contact','volunteer'
 */
$activePage = $activePage ?? '';
function nav_active(string $page, string $current): string {
    return $page === $current ? 'active' : '';
}
?>
<nav class="navbar navbar-expand-lg navbar-light kg-navbar sticky-top" id="mainNav">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
      <span class="kg-logo-icon"><i class="bi bi-flower2"></i></span>
      <span class="kg-brand-text">
        <span class="kg-brand-main">Kamadhenu</span>
        <span class="kg-brand-sub">Goushala</span>
      </span>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item">
          <a class="nav-link <?= nav_active('home', $activePage) ?>" href="<?= BASE_URL ?>/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('about', $activePage) ?>" href="<?= BASE_URL ?>/about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('cows', $activePage) ?>" href="<?= BASE_URL ?>/cows.php">Our Cows</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('seva', $activePage) ?>" href="<?= BASE_URL ?>/seva.php">Seva</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('adopt', $activePage) ?>" href="<?= BASE_URL ?>/adopt.php">Adopt</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('products', $activePage) ?>" href="<?= BASE_URL ?>/products.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('gallery', $activePage) ?>" href="<?= BASE_URL ?>/gallery.php">Gallery</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('events', $activePage) ?>" href="<?= BASE_URL ?>/events.php">Events</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= nav_active('contact', $activePage) ?>" href="<?= BASE_URL ?>/contact.php">Contact</a>
        </li>
        <li class="nav-item ms-lg-2">
          <a class="btn kg-btn-donate" href="<?= BASE_URL ?>/donate.php">
            <i class="bi bi-heart-fill me-1"></i> Donate
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
