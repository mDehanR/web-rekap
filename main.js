/* ================================================
   REKAP NILAI — Main JavaScript
   Professional scroll animations & interactions
   ================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ── 1. SCROLL REVEAL ── */
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        // Unobserve after reveal (one-shot)
        revealObserver.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -40px 0px'
  });

  document.querySelectorAll('[data-reveal]').forEach(el => {
    revealObserver.observe(el);
  });

  /* ── 2. STAGGERED CHILDREN ── */
  document.querySelectorAll('[data-stagger]').forEach(parent => {
    Array.from(parent.children).forEach((child, i) => {
      child.setAttribute('data-reveal', '');
      child.style.transitionDelay = (i * 0.07) + 's';
      revealObserver.observe(child);
    });
  });

  /* ── 3. PROGRESS BARS ANIMATE ── */
  const barObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const fill = entry.target.querySelector('.progress-fill');
        if (fill) {
          const target = fill.dataset.target || '0';
          fill.style.width = target + '%';
        }
        barObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  document.querySelectorAll('.progress-bar').forEach(bar => {
    const fill = bar.querySelector('.progress-fill');
    if (fill) {
      fill.style.width = '0%';
      barObserver.observe(bar);
    }
  });

  /* ── 4. COUNTER ANIMATION ── */
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseFloat(el.dataset.count || '0');
        const decimals = el.dataset.decimals ? parseInt(el.dataset.decimals) : 0;
        const duration = 1400;
        const start = performance.now();

        const tick = (now) => {
          const elapsed = now - start;
          const progress = Math.min(elapsed / duration, 1);
          // Ease out
          const eased = 1 - Math.pow(1 - progress, 3);
          const value = (target * eased).toFixed(decimals);
          el.textContent = value;
          if (progress < 1) requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-count]').forEach(el => {
    counterObserver.observe(el);
  });

  /* ── 5. SIDEBAR MOBILE TOGGLE ── */
  const sidebar      = document.getElementById('sidebar');
  const overlay      = document.getElementById('sidebarOverlay');
  const hamburger    = document.getElementById('hamburger');

  if (hamburger && sidebar && overlay) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('open');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
    });
  }

  /* ── 6. LIVE NILAI CALCULATOR ── */
  const tugasInput  = document.getElementById('tugas');
  const utsInput    = document.getElementById('uts');
  const uasInput    = document.getElementById('uas');
  const previewVal  = document.getElementById('previewNilai');
  const previewHuruf= document.getElementById('previewHuruf');
  const previewPred = document.getElementById('previewPredikat');

  function updateCalc() {
    const t = parseFloat(tugasInput?.value) || 0;
    const u = parseFloat(utsInput?.value)  || 0;
    const a = parseFloat(uasInput?.value)  || 0;
    const akhir = (t * 0.3) + (u * 0.3) + (a * 0.4);

    if (previewVal) previewVal.textContent = akhir.toFixed(2);

    let huruf, predikat, color;
    if      (akhir >= 90) { huruf = 'A';  predikat = 'Sangat Baik'; color = '#34d399'; }
    else if (akhir >= 80) { huruf = 'B+'; predikat = 'Baik';        color = '#7ab3ff'; }
    else if (akhir >= 75) { huruf = 'B';  predikat = 'Baik';        color = '#4f8ef7'; }
    else if (akhir >= 70) { huruf = 'C+'; predikat = 'Cukup';       color = '#fbbf24'; }
    else if (akhir >= 65) { huruf = 'C';  predikat = 'Cukup';       color = '#e8ba30'; }
    else if (akhir >= 55) { huruf = 'D';  predikat = 'Kurang';      color = '#ff9090'; }
    else                  { huruf = 'E';  predikat = 'Sangat Kurang'; color = '#f87171'; }

    if (previewHuruf) {
      previewHuruf.textContent = huruf;
      previewHuruf.style.color = color;
    }
    if (previewPred) {
      previewPred.textContent  = predikat;
      previewPred.style.color  = color;
    }
    if (previewVal) previewVal.style.color = color;
  }

  [tugasInput, utsInput, uasInput].forEach(inp => {
    if (inp) {
      inp.addEventListener('input', updateCalc);
      // Clamp 0-100
      inp.addEventListener('change', () => {
        if (parseFloat(inp.value) > 100) inp.value = 100;
        if (parseFloat(inp.value) < 0)   inp.value = 0;
        updateCalc();
      });
    }
  });

  updateCalc(); // run on load

  /* ── 7. SEARCH / FILTER TABLE ── */
  const searchInput = document.getElementById('searchInput');
  const tableBody   = document.querySelector('#dataTable tbody');

  if (searchInput && tableBody) {
    searchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase();
      const rows  = tableBody.querySelectorAll('tr');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });

      // Empty state
      const visible = Array.from(rows).filter(r => r.style.display !== 'none').length;
      const noResult = document.getElementById('noResult');
      if (noResult) noResult.style.display = visible === 0 ? '' : 'none';
    });
  }

  /* ── 8. CONFIRM DELETE ── */
  document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const msg = this.dataset.confirm || 'Yakin ingin menghapus data ini?';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  /* ── 9. TOAST NOTIFICATIONS ── */
  window.showToast = function (msg, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icon = type === 'success' ? '✓' : '✕';
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <span style="color: ${type === 'success' ? '#34d399' : '#f87171'}; font-weight:700; font-size:16px;">${icon}</span>
      <span>${msg}</span>
    `;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.transition = 'all 0.4s ease';
      toast.style.opacity    = '0';
      toast.style.transform  = 'translateX(40px)';
      setTimeout(() => toast.remove(), 400);
    }, 3000);
  };

  // Auto show toast from URL params
  const params = new URLSearchParams(location.search);
  if (params.get('success') === '1') showToast('Data berhasil disimpan!', 'success');
  if (params.get('success') === '2') showToast('Data berhasil diperbarui!', 'success');
  if (params.get('deleted') === '1') showToast('Data berhasil dihapus.', 'success');
  if (params.get('error')   === '1') showToast('Terjadi kesalahan, coba lagi.', 'error');

  /* ── 10. ACTIVE NAV HIGHLIGHT ── */
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-item, .bottom-nav-item').forEach(link => {
    const href = link.getAttribute('href');
    if (href && currentPath.endsWith(href.split('/').pop())) {
      link.classList.add('active');
    }
  });

  /* ── 11. MICRO RIPPLE on buttons ── */
  document.querySelectorAll('.btn-primary, .btn-login, .btn-primary-hero').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const ripple = document.createElement('span');
      const rect   = this.getBoundingClientRect();
      const size   = Math.max(rect.width, rect.height);
      const x      = e.clientX - rect.left - size / 2;
      const y      = e.clientY - rect.top  - size / 2;

      Object.assign(ripple.style, {
        width:      size + 'px',
        height:     size + 'px',
        left:       x + 'px',
        top:        y + 'px',
        position:   'absolute',
        borderRadius: '50%',
        background: 'rgba(255,255,255,0.25)',
        transform:  'scale(0)',
        animation:  'ripple 0.5s ease-out forwards',
        pointerEvents: 'none'
      });

      this.style.position = 'relative';
      this.style.overflow = 'hidden';
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 600);
    });
  });

  // Ripple keyframe (inject once)
  if (!document.getElementById('rippleStyle')) {
    const style = document.createElement('style');
    style.id = 'rippleStyle';
    style.textContent = `
      @keyframes ripple {
        to { transform: scale(2.5); opacity: 0; }
      }
    `;
    document.head.appendChild(style);
  }

});
