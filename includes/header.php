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
  <link rel="icon" href="<?= get_site_favicon_url() ?>" type="<?= get_site_favicon_type() ?>">

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
  
  <script>
  // Prevent Google Translate FOUT (Flash of English) on navigation
  if (document.cookie.indexOf('googtrans=') !== -1 && document.cookie.indexOf('googtrans=/en/en') === -1) {
      document.documentElement.classList.add('hide-for-translate');
      
      // Watch for Google Translate to finish and add its class
      var observer = new MutationObserver(function(mutations) {
          if (document.documentElement.classList.contains('translated-ltr') || document.documentElement.classList.contains('translated-rtl')) {
              document.documentElement.classList.remove('hide-for-translate');
              observer.disconnect();
          }
      });
      observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

      // Fallback in case Google Translate fails or takes too long (max 2 seconds)
      setTimeout(function() {
          document.documentElement.classList.remove('hide-for-translate');
          if (observer) observer.disconnect();
      }, 2000);
  }
  </script>
  <style>
  /* Hide content while translating to prevent flicker */
  html.hide-for-translate body {
      opacity: 0 !important;
      visibility: hidden !important;
  }
  /* Smooth fade in once translated */
  html.translated-ltr body, html.translated-rtl body {
      animation: fadeInTranslate 0.4s ease forwards;
  }
  @keyframes fadeInTranslate {
      from { opacity: 0; visibility: hidden; }
      to { opacity: 1; visibility: visible; }
  }
  </style>
</head>
<body>

<!-- Grand Preloader -->
<div id="kg-preloader" class="kg-preloader">
  <div class="kg-preloader-content">
    <div class="kg-preloader-logo-wrap">
      <img src="<?= get_site_favicon_url() ?>" alt="Loading" class="kg-preloader-logo">
      <div class="kg-preloader-ring"></div>
    </div>
    <div class="kg-preloader-text">
      <span class="kg-preloader-text-main">Kamadhenu</span>
      <span class="kg-preloader-text-sub">Goushala</span>
    </div>
  </div>
</div>
<script>
// Check if the page was refreshed (reloaded). If not, hide preloader instantly.
if (performance.navigation.type !== 1) {
    document.getElementById('kg-preloader').style.display = 'none';
}
</script>
