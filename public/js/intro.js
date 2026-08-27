/* ===== INTRO JS — Ferrari-style logo sequence ===== */
(function () {
    'use strict';

    /* ── Generate particles ──────────────────────────────── */
    function spawnParticles(overlay) {
        for (let i = 0; i < 24; i++) {
            const p = document.createElement('div');
            p.className = 'intro-particle';
            const angle = (i / 24) * 360;
            const dist  = 60 + Math.random() * 60;
            const tx = Math.cos(angle * Math.PI / 180) * dist;
            const ty = Math.sin(angle * Math.PI / 180) * dist;
            p.style.cssText = `
                left: 50%; top: 50%;
                --tx: ${tx}px; --ty: ${ty}px;
                animation-delay: ${0.8 + Math.random() * 0.4}s;
                animation-duration: ${1.5 + Math.random() * 0.8}s;
                box-shadow: 0 0 4px 1px rgba(0,135,81,0.9);
                background: ${Math.random() > 0.5 ? '#10b981' : '#00c966'};
                width: ${1 + Math.random() * 3}px;
                height: ${1 + Math.random() * 3}px;
            `;
            overlay.appendChild(p);
        }
    }

    /* ── Build overlay DOM ───────────────────────────────── */
    function buildOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'intro-overlay';

        overlay.innerHTML = `
            <!-- Curtains -->
            <div class="curtain">
                <div class="curtain-left"></div>
                <div class="curtain-right"></div>
            </div>

            <!-- Ambient glow -->
            <div class="intro-glow intro-glow-1"></div>
            <div class="intro-glow intro-glow-2"></div>

            <!-- Horizontal speed lines -->
            <div class="speed-line speed-line-left"></div>
            <div class="speed-line speed-line-right"></div>

            <!-- Corner accents -->
            <div class="intro-corner tl"></div>
            <div class="intro-corner tr"></div>
            <div class="intro-corner bl"></div>
            <div class="intro-corner br"></div>

            <!-- Main stage -->
            <div class="intro-stage">
                <div class="intro-logo-wrap">
                    <div class="intro-logo-ring"></div>
                    <div class="intro-logo-ring-2"></div>
                    <div class="intro-logo-circle">
                        <img
                            src="${(typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/img/logo.png'}"
                            alt="OCP Logo"
                            onerror="this.style.display='none'; this.parentElement.innerHTML += '<span style=\\'font-family:Orbitron,sans-serif;font-size:2.6rem;font-weight:900;color:#008751;text-shadow:0 0 20px rgba(0,135,81,0.9);letter-spacing:2px;\\'>OCP</span>'"
                        />
                    </div>
                </div>

                <div class="intro-brand">
                    <div class="intro-brand-title"><span>OCP</span> Stagiaires</div>
                    <div class="intro-divider"></div>
                    <div class="intro-brand-sub">Plateforme de Gestion · Digitalisation & SI</div>
                </div>

                <div class="intro-progress-wrap">
                    <div class="intro-progress-bar" id="intro-progress-bar"></div>
                </div>
            </div>
        `;

        document.body.prepend(overlay);
        spawnParticles(overlay);
        return overlay;
    }

    /* ── Sequence controller ─────────────────────────────── */
    function runIntro() {

        const overlay = buildOverlay();

        // Lock scroll during intro
        document.documentElement.style.overflow = 'hidden';

        // Step 1: after progress bar completes (~3.2s), start exit
        const exitDelay = 3400;

        setTimeout(function () {
            overlay.classList.add('exit');

                setTimeout(function () {
                overlay.remove();
                document.documentElement.style.overflow = '';
            }, 800);
        }, exitDelay);

        // Click to skip
        overlay.addEventListener('click', function () {
            overlay.style.animation = 'overlayExit 0.5s cubic-bezier(0.77,0,0.18,1) forwards';
            setTimeout(function () {
                overlay.remove();
                document.documentElement.style.overflow = '';
            }, 500);
        }, { once: true });
    }

    /* ── Boot ────────────────────────────────────────────── */
    // Only run intro when explicitly requested (e.g. after logout)
    if (window.ocp_show_intro_on_logout === true) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runIntro);
        } else {
            runIntro();
        }
    }
})();
