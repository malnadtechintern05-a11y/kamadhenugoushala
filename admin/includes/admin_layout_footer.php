  </div><!-- /.kg-admin-content -->
</main><!-- /.kg-admin-main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
<script>
// Preloader Logic for Admin Panel
window.addEventListener('load', function() {
  const preloader = document.getElementById('kg-preloader');
  // Only trigger the fade out animation if it wasn't hidden instantly in the header
  if (preloader && preloader.style.display !== 'none') {
    setTimeout(() => {
      preloader.classList.add('fade-out');
      // Remove it from the DOM after transition completes to free memory
      setTimeout(() => {
        preloader.remove();
      }, 800);
    }, 400);
  }
});

// Theme Toggle Logic
document.addEventListener('DOMContentLoaded', () => {
    const themeBtn = document.getElementById('themeToggleBtn');
    const themeIcon = document.getElementById('themeIcon');
    const htmlEl = document.documentElement;

    // Update icon on initial load
    if (htmlEl.getAttribute('data-bs-theme') === 'dark') {
        themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
    }

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const currentTheme = htmlEl.getAttribute('data-bs-theme');
            if (currentTheme === 'dark') {
                htmlEl.removeAttribute('data-bs-theme');
                localStorage.removeItem('theme');
                themeIcon.classList.replace('bi-sun', 'bi-moon-stars');
            } else {
                htmlEl.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
            }
        });
    }
});
</script>
</body>
</html>
