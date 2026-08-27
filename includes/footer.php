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
              <span class="kg-logo-icon-sm"><?= get_cow_logo_svg() ?></span>
              <span>
                <span class="kg-footer-brand-main"><?= e(explode(' ', SITE_NAME)[0] ?? 'Kamadhenu') ?></span>
                <span class="kg-footer-brand-sub"> <?= e(implode(' ', array_slice(explode(' ', SITE_NAME), 1)) ?? 'Goushala') ?></span>
              </span>
            </div>
            <p class="kg-footer-desc">
              <?= e(SITE_TAGLINE) ?>
            </p>
            <div class="d-flex gap-3 mt-3">
              <a href="<?= e(defined('SOCIAL_FACEBOOK') ? SOCIAL_FACEBOOK : '#') ?>" class="kg-social-link" aria-label="Facebook" target="_blank"><i class="bi bi-facebook"></i></a>
              <a href="<?= e(defined('SOCIAL_INSTAGRAM') ? SOCIAL_INSTAGRAM : '#') ?>" class="kg-social-link" aria-label="Instagram" target="_blank"><i class="bi bi-instagram"></i></a>
              <a href="<?= e(defined('SOCIAL_TWITTER') ? SOCIAL_TWITTER : '#') ?>" class="kg-social-link" aria-label="Twitter" target="_blank"><i class="bi bi-twitter-x"></i></a>
              <a href="<?= e(defined('SOCIAL_WHATSAPP') ? SOCIAL_WHATSAPP : '#') ?>" class="kg-social-link" aria-label="WhatsApp" target="_blank"><i class="bi bi-whatsapp"></i></a>
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
              <li><a href="<?= BASE_URL ?>/donate.php" target="_blank">Donate</a></li>
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

  <!-- Cart Offcanvas -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" data-bs-focus="false" data-bs-scroll="true">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="cartOffcanvasLabel">
        <i class="bi bi-cart3 me-2"></i>Your Cart
      </h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column" id="cart-offcanvas-body">
      <!-- Cart contents will be loaded here via AJAX -->
      <div class="text-center text-muted my-auto">
        <div class="spinner-border text-success" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="<?= BASE_URL ?>/assets/js/script.js"></script>
  <script>
  // Cart JS logic
  document.addEventListener('DOMContentLoaded', function() {
      var cartOffcanvas = document.getElementById('cartOffcanvas');
      if(cartOffcanvas) {
          cartOffcanvas.addEventListener('show.bs.offcanvas', function () {
              loadCart();
          });
      }
  });

  function loadCart() {
      fetch('<?= BASE_URL ?>/cart_action.php?action=view')
      .then(response => response.text())
      .then(html => {
          document.getElementById('cart-offcanvas-body').innerHTML = html;
      });
  }

  function addToCart(productId, quantity = 1) {
      var formData = new FormData();
      formData.append('action', 'add');
      formData.append('product_id', productId);
      formData.append('quantity', quantity);

      fetch('<?= BASE_URL ?>/cart_action.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if(data.success) {
              updateCartBadge(data.count);
              // open offcanvas
              var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('cartOffcanvas'));
              bsOffcanvas.show();
          } else {
              alert(data.message || 'Error adding to cart');
          }
      });
  }

  function updateCartItem(productId, quantity) {
      var formData = new FormData();
      formData.append('action', 'update');
      formData.append('product_id', productId);
      formData.append('quantity', quantity);

      fetch('<?= BASE_URL ?>/cart_action.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if(data.success) {
              updateCartBadge(data.count);
              loadCart();
          }
      });
  }

  function removeFromCart(productId) {
      updateCartItem(productId, 0);
  }

  function updateCartBadge(count) {
      var badge = document.getElementById('cart-count-badge');
      if(badge) {
          badge.innerText = count;
          badge.style.display = count > 0 ? 'inline-block' : 'none';
      }
  }
  </script>
  <!-- 3D Vanta Background Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.fog.min.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    var vantaTargets = document.querySelectorAll(".kg-hero, .kg-page-banner");
    vantaTargets.forEach(function(el) {
      VANTA.FOG({
        el: el,
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.00,
        minWidth: 200.00,
        highlightColor: 0x7a966e, // Green Light
        midtoneColor: 0x4a6741,   // Green
        lowlightColor: 0x2d5236,  // Darker Green
        baseColor: 0x2d5236,      // Darker Green
        blurFactor: 0.60,
        speed: 1.50,
        zoom: 1.20
      });
    });
  });
  </script>

  <!-- Google Translate Widget (auto-translates page on load when cookie is set) -->
  <div id="google_translate_element" style="display:none;"></div>
  <script type="text/javascript">
  function googleTranslateElementInit() {
    new google.translate.TranslateElement({
      pageLanguage: 'en',
      includedLanguages: 'en,hi,kn',
      layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
      autoDisplay: false
    }, 'google_translate_element');
  }

  /**
   * Language Switcher — Server-side approach.
   * POSTs to set_language.php which sets the googtrans cookie via PHP (HTTP header level),
   * guaranteed to work on any hosting including free hosts.
   */
  function switchLanguage(lang) {
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = '<?= BASE_URL ?>/set_language.php';
      form.style.display = 'none';

      var langField = document.createElement('input');
      langField.type = 'hidden';
      langField.name = 'lang';
      langField.value = lang;
      form.appendChild(langField);

      var redirectField = document.createElement('input');
      redirectField.type = 'hidden';
      redirectField.name = 'redirect';
      redirectField.value = window.location.href;
      form.appendChild(redirectField);

      document.body.appendChild(form);
      form.submit();
  }

  document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.language-select').forEach(function(el) {
          el.addEventListener('click', function(e) {
              e.preventDefault();
              switchLanguage(this.getAttribute('data-lang'));
          });
      });
  });
  </script>
  <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

  <style>
  /* Aggressively Hide the Google Translate Top Banner */
  .goog-te-banner-frame.skiptranslate,
  .goog-te-banner-frame { 
      display: none !important; 
      visibility: hidden !important;
  }
  body { 
      top: 0px !important; 
      position: relative !important;
  }
  html {
      height: 100%;
  }
  /* Hide the Google Translate tooltip */
  #goog-gt-tt { display: none !important; }
  .goog-tooltip { display: none !important; }
  .goog-tooltip:hover { display: none !important; }
  /* Hide newer versions of the widget iframe */
  .VIpgJd-ZVi9od-ORHb-OEVmcd { display: none !important; }
  iframe.VIpgJd-ZVi9od-aZ2wEe-wOHMyf { display: none !important; }
  </style>

  <script>
  // Animate elements smoothly on scroll
  document.addEventListener("DOMContentLoaded", function() {
    const observerOptions = {
      root: null,
      rootMargin: '0px 0px -50px 0px',
      threshold: 0
    };

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Auto-apply animation class to all sections and information blocks
    const elementsToAnimate = document.querySelectorAll('section, .kg-section, .kg-cow-card, .impact-card, .kg-form-card, .kg-product-card, .kg-page-header, .kg-footer-top, .card, .gallery-item, .row > [class*="col-"]');
    elementsToAnimate.forEach(el => {
      el.classList.add('scroll-fade-up');
      observer.observe(el);
    });
  });

  // Preloader Logic
  window.addEventListener('load', function() {
    const preloader = document.getElementById('kg-preloader');
    // Only trigger the fade out animation if it wasn't hidden instantly in the header
    if (preloader && preloader.style.display !== 'none') {
      // If translation is active, give it a bit more time to translate before fading out
      let delay = 400;
      if (document.cookie.indexOf('googtrans=') !== -1 && document.cookie.indexOf('googtrans=/en/en') === -1) {
          delay = 800; // wait longer for google translate to finish
      }
      // Add a slight delay for dramatic effect / translation
      setTimeout(() => {
        preloader.classList.add('fade-out');
        // Remove it from the DOM after transition completes to free memory
        setTimeout(() => {
          preloader.remove();
        }, 800);
      }, delay);
    }
  });
  </script>
  </body>
  </html>
