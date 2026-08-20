<?php
/**
 * Footer — Kamadhenu Goushala
 */
?>
<footer class="kg-footer mt-auto">
  <div class="kg-footer-top">
    <div class="container">
      <div class="row g-5">
        <!-- Brand & About -->
        <div class="col-lg-4 col-md-6">
          <div class="kg-footer-brand d-flex align-items-center gap-2 mb-3">
            <span class="kg-logo-icon-sm"><i class="bi bi-flower2"></i></span>
            <span>
              <span class="kg-footer-brand-main">Kamadhenu</span>
              <span class="kg-footer-brand-sub"> Goushala</span>
            </span>
          </div>
          <p class="kg-footer-desc">
            A sacred sanctuary dedicated to the protection, care, and reverence of indigenous Indian cows (Gau Mata). Serving with devotion since 1998.
          </p>
          <div class="d-flex gap-3 mt-3">
            <a href="#" class="kg-social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="kg-social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="kg-social-link" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="#" class="kg-social-link" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-6">
          <h6 class="kg-footer-heading">Quick Links</h6>
          <ul class="kg-footer-links">
            <li><a href="<?= BASE_URL ?>/">Home</a></li>
            <li><a href="<?= BASE_URL ?>/about.php">About Us</a></li>
            <li><a href="<?= BASE_URL ?>/cows.php">Our Cows</a></li>
            <li><a href="<?= BASE_URL ?>/seva.php">Seva</a></li>
            <li><a href="<?= BASE_URL ?>/gallery.php">Gallery</a></li>
          </ul>
        </div>

        <!-- Services -->
        <div class="col-lg-2 col-md-6">
          <h6 class="kg-footer-heading">Get Involved</h6>
          <ul class="kg-footer-links">
            <li><a href="<?= BASE_URL ?>/donate.php">Donate</a></li>
            <li><a href="<?= BASE_URL ?>/adopt.php">Adopt a Cow</a></li>
            <li><a href="<?= BASE_URL ?>/volunteer.php">Volunteer</a></li>
            <li><a href="<?= BASE_URL ?>/products.php">Products</a></li>
            <li><a href="<?= BASE_URL ?>/contact.php">Contact Us</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="col-lg-4 col-md-6">
          <h6 class="kg-footer-heading">Contact Us</h6>
          <ul class="kg-footer-contact">
            <li>
              <i class="bi bi-geo-alt-fill"></i>
              <span><?= e(SITE_ADDRESS) ?></span>
            </li>
            <li>
              <i class="bi bi-telephone-fill"></i>
              <a href="tel:<?= e(SITE_PHONE) ?>"><?= e(SITE_PHONE) ?></a>
            </li>
            <li>
              <i class="bi bi-envelope-fill"></i>
              <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a>
            </li>
            <li>
              <i class="bi bi-clock-fill"></i>
              <span>Open daily: 5:00 AM – 8:00 PM</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="kg-footer-bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0">© <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</p>
        </div>
        <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
          <p class="mb-0">
            <span class="kg-om-symbol">ॐ</span> गौ माता की जय
            &nbsp;|&nbsp;
            <a href="<?= BASE_URL ?>/admin/login.php" class="kg-footer-admin-link">Admin</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>
</html>
