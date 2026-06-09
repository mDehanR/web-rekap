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

  /* ── 12. HERO PARALLAX (homepage) ── */
  const heroWrapper = document.querySelector('.hero-wrapper');
  const heroOrbs = document.querySelectorAll('.mesh-orb');
  if (heroWrapper && heroOrbs.length > 0) {
    window.addEventListener('mousemove', (e) => {
      const x = (e.clientX / window.innerWidth - 0.5);
      const y = (e.clientY / window.innerHeight - 0.5);
      heroWrapper.style.transform = `translate(${x * 10}px, ${y * 8}px)`;
      heroOrbs.forEach((orb, i) => {
        const factor = (i + 1) * 6;
        orb.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
      });
    });
  }
  /* ── 13. CUSTOM SELECT BEHAVIOR ── */
  function closeAllCustomSelects(except) {
    document.querySelectorAll('.custom-select.open').forEach(cs => {
      if (cs !== except) cs.classList.remove('open');
      const btn = cs.querySelector('.custom-select__trigger');
      if (btn) btn.setAttribute('aria-expanded', 'false');
    });
  }

  document.querySelectorAll('.custom-select').forEach(cs => {
    const trigger = cs.querySelector('.custom-select__trigger');
    const list = cs.querySelector('.custom-select__options');
    const input = cs.querySelector('input[type="hidden"]');

    // Open/close on click
    trigger.addEventListener('click', (e) => {
      const isOpen = cs.classList.toggle('open');
      trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (isOpen) {
        closeAllCustomSelects(cs);
        // position popup (fixed) below trigger
        const rect = trigger.getBoundingClientRect();
        list.style.minWidth = rect.width + 'px';
        list.style.left = rect.left + 'px';
        // position below, with small gap
        list.style.top = (rect.bottom + 6) + 'px';
        // focus the list for keyboard
        list.querySelector('.custom-select__option')?.focus();
      } else {
        // clear inline positioning
        list.style.left = '';
        list.style.top = '';
        list.style.minWidth = '';
      }
    });

    // Option click
    list.querySelectorAll('.custom-select__option').forEach(opt => {
      opt.addEventListener('click', () => {
        const val = opt.dataset.value;
        const label = opt.textContent.trim();
        // set visible text
        cs.querySelector('.custom-select__value').textContent = label.replace(/^✓\s*/,'');
        // set hidden input
        if (input) input.value = val;
        // mark selected
        list.querySelectorAll('.custom-select__option').forEach(o => o.setAttribute('aria-selected','false'));
        opt.setAttribute('aria-selected','true');
        cs.classList.remove('open');
        trigger.setAttribute('aria-expanded','false');
      });
    });

    // Close on outside click
    document.addEventListener('click', (ev) => {
      if (!cs.contains(ev.target)) {
        cs.classList.remove('open');
        trigger.setAttribute('aria-expanded','false');
        // clear inline positioning
        list.style.left = '';
        list.style.top = '';
        list.style.minWidth = '';
      }
    });

    // Keyboard support
    trigger.addEventListener('keydown', (ev) => {
      if (ev.key === 'ArrowDown' || ev.key === 'Enter' || ev.key === ' ') {
        ev.preventDefault();
        cs.classList.add('open');
        trigger.setAttribute('aria-expanded','true');
        list.querySelector('.custom-select__option')?.focus();
      }
    });

    list.querySelectorAll('.custom-select__option').forEach((opt, idx, arr) => {
      opt.setAttribute('tabindex','0');
      opt.addEventListener('keydown', (ev) => {
        if (ev.key === 'ArrowDown') { ev.preventDefault(); arr[(idx+1)%arr.length].focus(); }
        if (ev.key === 'ArrowUp')   { ev.preventDefault(); arr[(idx-1+arr.length)%arr.length].focus(); }
        if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); opt.click(); }
        if (ev.key === 'Escape') { cs.classList.remove('open'); trigger.focus(); }
      });
    });
  });

  /* ── 14. MATPEL BUTTONS (replace dropdown) ── */
  (function () {
    const hidden = document.getElementById('mata_pelajaran');
    const buttons = document.querySelectorAll('.matpel-button');
    if (!buttons || buttons.length === 0) return;

    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        if (hidden) hidden.value = btn.dataset.value || '';
      });
      // keyboard support: Enter / Space
      btn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          btn.click();
        }
      });
    });

    // initialize active from hidden value
    if (hidden && hidden.value) {
      buttons.forEach(b => {
        if (b.dataset.value === hidden.value) b.classList.add('active');
      });
    }
  })();

});
