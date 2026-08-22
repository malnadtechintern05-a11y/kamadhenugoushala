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
    <a class="navbar-brand kg-navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
      <span class="kg-logo-icon"><?= get_cow_logo_svg() ?></span>
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
        <!-- Language Switcher -->
        <li class="nav-item dropdown ms-lg-2">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-translate fs-5"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="languageDropdown">
            <li><a class="dropdown-item language-select" href="#" data-lang="en">English</a></li>
            <li><a class="dropdown-item language-select" href="#" data-lang="hi">हिंदी (Hindi)</a></li>
            <li><a class="dropdown-item language-select" href="#" data-lang="kn">ಕನ್ನಡ (Kannada)</a></li>
          </ul>
        </li>
        <li class="nav-item ms-lg-2 position-relative">
          <a class="nav-link" href="#" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas" title="View Cart">
            <i class="bi bi-cart3 fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count-badge" style="font-size: 0.65rem; margin-top: 10px; margin-left: -10px; display: <?= get_cart_count() > 0 ? 'inline-block' : 'none' ?>;">
              <?= get_cart_count() ?>
            </span>
          </a>
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
