/**
 * Application Core JS - OCP Intern Management Platform v2.0
 * Dark/Light mode toggle + utilities
 */

function getApiUrl(path) {
    const base = (typeof BASE_URL !== 'undefined' ? BASE_URL : '').replace(/\/+$/, '');
    return base + '/' + path.replace(/^\/+/, '');
}

document.addEventListener('DOMContentLoaded', function () {

    // ── Dark / Light Mode Toggle ────────────────────────────────────────────
    const html = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');

    // Apply saved theme immediately on load
    const savedTheme = localStorage.getItem('ocp-theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    updateToggleIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('ocp-theme', next);
            updateToggleIcon(next);

            // Small animation
            themeToggle.style.transform = 'rotate(360deg)';
            setTimeout(() => themeToggle.style.transform = '', 400);
        });
    }

    function updateToggleIcon(theme) {
        if (!themeToggle) return;
        if (theme === 'dark') {
            themeToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
            themeToggle.title = 'Passer en mode clair';
        } else {
            themeToggle.innerHTML = '<i class="bi bi-moon-fill"></i>';
            themeToggle.title = 'Passer en mode sombre';
        }
    }

    // ── Auto-dismiss alerts after 6 seconds ────────────────────────────────
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            if (alert && alert.parentNode) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 6000);
    });

    // ── Tooltips initialization ─────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // ── Sidebar mini-mode + mouse hover expand + toggle ─────────────────────
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');

    // Restore expanded state if user pinned it
    const isSidebarPinned = localStorage.getItem('ocp-sidebar-pinned') === 'true';
    if (isSidebarPinned && sidebar) {
        sidebar.classList.add('expanded');
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('expanded');
                localStorage.setItem('ocp-sidebar-pinned', sidebar.classList.contains('expanded'));
            }
        });

        // Close mobile overlay on outside click
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // ── Mark notification as read via AJAX ─────────────────────────────────
    document.querySelectorAll('.mark-notif-read').forEach(function (el) {
        el.addEventListener('click', function () {
            const notifId = this.getAttribute('data-id');
            const row = this.closest('[data-notif-id]') || this.closest('.list-group-item');
            fetch(getApiUrl('api/mark_notification_read'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + notifId
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && row) {
                        row.classList.remove('unread');
                        const badge = row.querySelector('.notif-unread-dot');
                        if (badge) badge.remove();
                        this.remove();
                    }
                })
                .catch(err => console.warn('Notification mark error:', err));
        });
    });

    // ── Progress range slider real-time display ─────────────────────────────
    document.querySelectorAll('input[type="range"][data-show-value]').forEach(function (slider) {
        const targetId = slider.getAttribute('data-show-value');
        const display = document.getElementById(targetId);
        if (display) {
            display.textContent = slider.value + '%';
            slider.addEventListener('input', function () {
                display.textContent = this.value + '%';
            });
        }
    });

    // ── Drag & Drop Upload Zone ─────────────────────────────────────────────
    const uploadZones = document.querySelectorAll('.upload-zone');
    uploadZones.forEach(function (zone) {
        const input = zone.querySelector('input[type="file"]') || document.querySelector(zone.getAttribute('data-input'));

        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            zone.classList.add('dragover');
        });

        zone.addEventListener('dragleave', function () {
            zone.classList.remove('dragover');
        });

        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('dragover');
            if (input && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                const fileName = e.dataTransfer.files[0].name;
                const label = zone.querySelector('.upload-filename');
                if (label) label.textContent = fileName;
                zone.classList.add('file-selected');
            }
        });

        zone.addEventListener('click', function (e) {
            if (e.target.tagName !== 'INPUT' && input) {
                input.click();
            }
        });

        if (input) {
            input.addEventListener('change', function () {
                if (this.files.length) {
                    const label = zone.querySelector('.upload-filename');
                    if (label) label.textContent = this.files[0].name;
                    zone.classList.add('file-selected');
                }
            });
        }
    });

    // ── Avatar preview ──────────────────────────────────────────────────────
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

});
