/**
 * Kamadhenu Goushala — Main JavaScript
 * Vanilla JS | Bootstrap 5 compatible
 */

/* ── Animated Counter ──────────────────────────────────────────────── */
function animateCounters() {
  const counters = document.querySelectorAll('[data-counter]');
  if (!counters.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el    = entry.target;
      const end   = parseInt(el.dataset.counter, 10);
      const dur   = 2000; // ms
      const step  = Math.ceil(end / (dur / 16));
      let   cur   = 0;
      const timer = setInterval(() => {
        cur = Math.min(cur + step, end);
        el.textContent = cur.toLocaleString('en-IN') + (el.dataset.suffix || '');
        if (cur >= end) clearInterval(timer);
      }, 16);
      observer.unobserve(el);
    });
  }, { threshold: 0.4 });

  counters.forEach(el => observer.observe(el));
}

/* ── Donation Amount Buttons ───────────────────────────────────────── */
function initDonationAmounts() {
  const btns  = document.querySelectorAll('.kg-amount-btn');
  const input = document.getElementById('donationAmount');
  if (!btns.length || !input) return;

  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      btns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      input.value = btn.dataset.amount;
    });
  });

  // Clear active state when user types in the input manually
  input.addEventListener('input', () => {
    btns.forEach(b => b.classList.remove('active'));
  });
}

/* ── Navbar Scroll Effect ──────────────────────────────────────────── */
function initNavbarScroll() {
  const nav = document.getElementById('mainNav');
  if (!nav) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 60) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
  }, { passive: true });
}

/* ── Smooth Scroll for Anchor Links ───────────────────────────────── */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });
}

/* ── Form Validation ───────────────────────────────────────────────── */
function initFormValidation() {
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });
}

/* ── Image Preview (admin uploads) ────────────────────────────────── */
function initImagePreview() {
  document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
    const previewId = input.dataset.preview;
    const preview   = document.getElementById(previewId);
    if (!preview) return;

    input.addEventListener('change', () => {
      const file = input.files[0];
      if (!file || !file.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = e => {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    });
  });
}

/* ── Gallery Lightbox (simple) ─────────────────────────────────────── */
function initGalleryLightbox() {
  const items = document.querySelectorAll('.kg-gallery-item[data-lightbox]');
  if (!items.length) return;

  // Create modal
  const modal = document.createElement('div');
  modal.id = 'kg-lightbox';
  modal.innerHTML = `
    <div class="kg-lightbox-overlay" id="kg-lightbox-overlay">
      <button class="kg-lightbox-close" id="kg-lightbox-close" aria-label="Close">&times;</button>
      <img src="" alt="" id="kg-lightbox-img">
      <p id="kg-lightbox-caption"></p>
    </div>`;
  document.body.appendChild(modal);

  // Inline styles
  const style = document.createElement('style');
  style.textContent = `
    #kg-lightbox { display:none; position:fixed; inset:0; z-index:9999; }
    .kg-lightbox-overlay { background:rgba(0,0,0,.9); display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; padding:2rem; }
    #kg-lightbox-img { max-width:90vw; max-height:80vh; object-fit:contain; border-radius:8px; animation:fadeIn .3s ease; }
    #kg-lightbox-caption { color:rgba(255,255,255,.8); margin-top:.75rem; font-size:.9rem; text-align:center; }
    .kg-lightbox-close { position:fixed; top:1rem; right:1.5rem; background:none; border:none; color:#fff; font-size:2.5rem; cursor:pointer; line-height:1; }
    @keyframes fadeIn { from { opacity:0; transform:scale(.95); } to { opacity:1; transform:scale(1); } }
  `;
  document.head.appendChild(style);

  const lb      = document.getElementById('kg-lightbox');
  const lbImg   = document.getElementById('kg-lightbox-img');
  const lbCap   = document.getElementById('kg-lightbox-caption');
  const lbClose = document.getElementById('kg-lightbox-close');

  items.forEach(item => {
    item.addEventListener('click', () => {
      lbImg.src       = item.dataset.lightbox;
      lbImg.alt       = item.dataset.title || '';
      lbCap.textContent = item.dataset.title || '';
      lb.style.display = 'block';
      document.body.style.overflow = 'hidden';
    });
  });

  lbClose.addEventListener('click', closeLightbox);
  lb.addEventListener('click', e => { if (e.target === document.getElementById('kg-lightbox-overlay') || e.target === lb) closeLightbox(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

  function closeLightbox() {
    lb.style.display = 'none';
    document.body.style.overflow = '';
  }
}

/* ── Confirm Delete ────────────────────────────────────────────────── */
function initConfirmDelete() {
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      const msg = el.dataset.confirm || 'Are you sure you want to delete this?';
      if (!confirm(msg)) e.preventDefault();
    });
  });
}

/* ── Admin Sidebar Toggle (mobile) ─────────────────────────────────── */
function initAdminSidebar() {
  const toggleBtn = document.getElementById('sidebarToggle');
  const sidebar   = document.getElementById('adminSidebar');
  if (!toggleBtn || !sidebar) return;

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
  });

  // Close on outside click
  document.addEventListener('click', e => {
    if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
      sidebar.classList.remove('show');
    }
  });
}

/* ── Flash alert auto-dismiss ──────────────────────────────────────── */
function initFlashAutoDismiss() {
  document.querySelectorAll('.alert.alert-success').forEach(alert => {
    setTimeout(() => {
      const bsAlert = window.bootstrap && new bootstrap.Alert(alert);
      if (bsAlert) bsAlert.close();
      else alert.remove();
    }, 5000);
  });
}

/* ── Init all ──────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  animateCounters();
  initDonationAmounts();
  initNavbarScroll();
  initSmoothScroll();
  initFormValidation();
  initImagePreview();
  initGalleryLightbox();
  initConfirmDelete();
  initAdminSidebar();
  initFlashAutoDismiss();
});
