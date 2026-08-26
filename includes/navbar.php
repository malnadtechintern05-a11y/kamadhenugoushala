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
        <?php
        $currentLangCode = 'en';
        if (isset($_COOKIE['googtrans'])) {
            $parts = explode('/', $_COOKIE['googtrans']);
            if (isset($parts[2]) && in_array($parts[2], ['en', 'hi', 'kn'])) {
                $currentLangCode = $parts[2];
            }
        }
        $langLabels = [
            'en' => 'English',
            'hi' => 'हिंदी',
            'kn' => 'ಕನ್ನಡ'
        ];
        $currentLabel = $langLabels[$currentLangCode] ?? 'English';
        ?>
        <li class="nav-item dropdown ms-lg-2">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: var(--kg-green-pale, rgba(45,82,54,0.06)); padding: 0.4rem 1rem; border-radius: 50px;">
            <i class="bi bi-globe-central-south-asia" style="color: var(--kg-green);"></i>
            <span class="fw-medium text-dark" style="font-size: 0.9rem;" id="currentLangLabel"><?= $currentLabel ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4" aria-labelledby="languageDropdown" style="min-width: 180px; padding: 0.5rem; border-top: 3px solid var(--kg-gold) !important;">
            <li>
              <a class="dropdown-item language-select rounded-3 d-flex align-items-center justify-content-between mb-1 <?= $currentLangCode === 'en' ? 'active' : '' ?>" href="#" data-lang="en" <?= $currentLangCode === 'en' ? 'style="background-color: var(--kg-green); color: #fff;"' : '' ?>>
                <span><span class="me-2 fs-5">🇬🇧</span>English</span>
                <?= $currentLangCode === 'en' ? '<i class="bi bi-check2"></i>' : '' ?>
              </a>
            </li>
            <li>
              <a class="dropdown-item language-select rounded-3 d-flex align-items-center justify-content-between mb-1 <?= $currentLangCode === 'hi' ? 'active' : '' ?>" href="#" data-lang="hi" <?= $currentLangCode === 'hi' ? 'style="background-color: var(--kg-green); color: #fff;"' : '' ?>>
                <span><span class="me-2 fs-5">🇮🇳</span>हिंदी (Hindi)</span>
                <?= $currentLangCode === 'hi' ? '<i class="bi bi-check2"></i>' : '' ?>
              </a>
            </li>
            <li>
              <a class="dropdown-item language-select rounded-3 d-flex align-items-center justify-content-between <?= $currentLangCode === 'kn' ? 'active' : '' ?>" href="#" data-lang="kn" <?= $currentLangCode === 'kn' ? 'style="background-color: var(--kg-green); color: #fff;"' : '' ?>>
                <span><span class="me-2 fs-5">🇮🇳</span>ಕನ್ನಡ (Kannada)</span>
                <?= $currentLangCode === 'kn' ? '<i class="bi bi-check2"></i>' : '' ?>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item ms-lg-2 position-relative">
          <a class="nav-link" href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas" title="View Cart">
            <i class="bi bi-cart3 fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count-badge" style="font-size: 0.65rem; margin-top: 10px; margin-left: -10px; display: <?= get_cart_count() > 0 ? 'inline-block' : 'none' ?>;">
              <?= get_cart_count() ?>
            </span>
          </a>
        </li>
        <li class="nav-item ms-lg-2 d-flex align-items-center">
          <a class="nav-link d-flex align-items-center" href="<?= BASE_URL ?>/supporter-dashboard.php" title="Supporter Account">
            <i class="bi bi-person-circle fs-5 me-1"></i>
            <?php if (isset($_SESSION['supporter_name'])): ?>
              <span class="fw-bold text-truncate" style="font-size: 0.9rem; max-width: 120px;"><?= e($_SESSION['supporter_name']) ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item ms-lg-2 d-flex align-items-center">
          <a class="btn kg-btn-donate d-flex align-items-center" target="_blank" href="<?= BASE_URL ?>/donate.php">
            <i class="bi bi-heart-fill me-1"></i> Donate
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
