/**
 * SMK Pertamaku - Main JavaScript
 */

/* ============================================================
   Theme Toggle
   ============================================================ */
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
}

function updateThemeIcon(theme) {
    const btn = document.getElementById('themeToggle');
    if (!btn) return;
    const icon = btn.querySelector('i');
    const text = btn.querySelector('span');
    if (theme === 'dark') {
        if (icon) { icon.className = 'fas fa-sun'; }
        if (text) { text.textContent = 'Light'; }
    } else {
        if (icon) { icon.className = 'fas fa-moon'; }
        if (text) { text.textContent = 'Dark'; }
    }
}

/* ============================================================
   Navbar Scroll Effect
   ============================================================ */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

/* ============================================================
   Back to Top Button
   ============================================================ */
function initBackToTop() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    });
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/* ============================================================
   Counter Animation for Stats
   ============================================================ */
function animateCounter(el, target, duration) {
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start += step;
        if (start >= target) {
            el.textContent = target.toLocaleString('id-ID');
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(start).toLocaleString('id-ID');
        }
    }, 16);
}

function initCounters() {
    const counters = document.querySelectorAll('.stat-number[data-target]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-target'), 10);
                animateCounter(el, target, 1500);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));
}

/* ============================================================
   Gallery Lightbox
   ============================================================ */
function initLightbox() {
    const items = document.querySelectorAll('.gallery-item[data-src]');
    const modal = document.getElementById('lightboxModal');
    if (!items.length || !modal) return;

    const img = modal.querySelector('#lightboxImg');
    const caption = modal.querySelector('#lightboxCaption');
    const bsModal = new bootstrap.Modal(modal);

    items.forEach(item => {
        item.addEventListener('click', () => {
            img.src = item.getAttribute('data-src');
            if (caption) caption.textContent = item.getAttribute('data-title') || '';
            bsModal.show();
        });
    });
}

/* ============================================================
   SPMB Multi-step Form
   ============================================================ */
function initMultiStep() {
    const form = document.getElementById('spmbForm');
    if (!form) return;

    let currentStep = 0;
    const panels = form.querySelectorAll('.step-panel');
    const stepItems = document.querySelectorAll('.step-item');
    const nextBtns = form.querySelectorAll('.btn-next');
    const prevBtns = form.querySelectorAll('.btn-prev');

    function showStep(n) {
        panels.forEach((p, i) => {
            p.classList.toggle('active', i === n);
        });
        stepItems.forEach((s, i) => {
            s.classList.remove('active', 'done');
            if (i === n) s.classList.add('active');
            if (i < n) s.classList.add('done');
        });
        currentStep = n;
    }

    function validateStep(n) {
        const panel = panels[n];
        const required = panel.querySelectorAll('[required]');
        let valid = true;
        required.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        return valid;
    }

    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (validateStep(currentStep) && currentStep < panels.length - 1) {
                // Build preview on last step
                if (currentStep === panels.length - 2) buildPreview();
                showStep(currentStep + 1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 0) {
                showStep(currentStep - 1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    // Show first step
    showStep(0);
}

/* ============================================================
   Build Preview for SPMB form step 6
   ============================================================ */
function buildPreview() {
    const form = document.getElementById('spmbForm');
    if (!form) return;
    const preview = document.getElementById('previewData');
    if (!preview) return;

    const fields = form.querySelectorAll('input, select, textarea');
    let html = '<dl class="row">';
    fields.forEach(f => {
        if (!f.name || f.type === 'hidden' || f.type === 'file') return;
        const label = form.querySelector('label[for="' + f.id + '"]');
        const labelText = label ? label.textContent.replace('*','').trim() : f.name;
        let val = f.value;
        if (f.tagName === 'SELECT') {
            const opt = f.options[f.selectedIndex];
            val = opt ? opt.text : val;
        }
        if (val) {
            html += `<dt class="col-sm-4">${labelText}</dt><dd class="col-sm-8">${val}</dd>`;
        }
    });
    html += '</dl>';
    preview.innerHTML = html;
}

/* ============================================================
   Auto Slug Generator (admin forms)
   ============================================================ */
function initAutoSlug() {
    const titleField = document.getElementById('title');
    const slugField = document.getElementById('slug');
    if (!titleField || !slugField) return;

    titleField.addEventListener('input', () => {
        if (slugField.dataset.auto !== 'false') {
            slugField.value = titleField.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
        }
    });

    slugField.addEventListener('input', () => {
        slugField.dataset.auto = 'false';
    });
}

/* ============================================================
   Image Upload Preview
   ============================================================ */
function initImagePreview() {
    document.querySelectorAll('.image-upload-input').forEach(input => {
        input.addEventListener('change', function () {
            const preview = document.querySelector(this.dataset.preview);
            if (!preview) return;
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    });
}

/* ============================================================
   Preloader
   ============================================================ */
function initPreloader() {
    const loader = document.getElementById('preloader');
    if (!loader) return;
    window.addEventListener('load', () => {
        loader.style.opacity = '0';
        setTimeout(() => { loader.style.display = 'none'; }, 500);
    });
}

/* ============================================================
   Admin Sidebar Toggle (mobile)
   ============================================================ */
function initAdminSidebar() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (!toggle || !sidebar) return;
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
}

/* ============================================================
   Alert Auto-dismiss
   ============================================================ */
function initAlertDismiss() {
    document.querySelectorAll('.alert-auto-dismiss').forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 4000);
    });
}

/* ============================================================
   Contact Form Char Counter
   ============================================================ */
function initCharCounters() {
    document.querySelectorAll('textarea[maxlength]').forEach(ta => {
        const counter = document.createElement('small');
        counter.className = 'text-muted';
        counter.textContent = `0/${ta.maxLength}`;
        ta.parentNode.appendChild(counter);
        ta.addEventListener('input', () => {
            counter.textContent = `${ta.value.length}/${ta.maxLength}`;
        });
    });
}

/* ============================================================
   Init All on DOMContentLoaded
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initNavbarScroll();
    initBackToTop();
    initCounters();
    initLightbox();
    initMultiStep();
    initAutoSlug();
    initImagePreview();
    initPreloader();
    initAdminSidebar();
    initAlertDismiss();
    initCharCounters();

    // Theme toggle button
    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', toggleTheme);
    }
});

// Init preloader immediately (not waiting for DOMContentLoaded)
initPreloader();
