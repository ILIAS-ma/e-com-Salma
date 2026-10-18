import { gsap } from 'gsap';

export function initNavbarGsap() {
    const nav = document.querySelector('[data-navbar-desktop]');

    if (!nav) {
        return;
    }

    const items = Array.from(nav.querySelectorAll('[data-nav-item]'));

    if (!items.length) {
        return;
    }

    let closeTimer;

    const clearCloseTimer = () => {
        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = undefined;
        }
    };

    const closePanel = (panel) => {
        if (!panel) {
            return;
        }

        gsap.killTweensOf(panel);
        gsap.to(panel, {
            autoAlpha: 0,
            y: -8,
            scale: 0.985,
            duration: 0.18,
            ease: 'power2.out',
            onComplete: () => {
                panel.style.display = 'none';
            },
        });
    };

    const closeAll = (exceptPanel = null) => {
        items.forEach((item) => {
            const panel = item.querySelector('[data-nav-panel]');

            if (panel && panel !== exceptPanel) {
                closePanel(panel);
            }
        });
    };

    const openPanel = (panel) => {
        if (!panel) {
            return;
        }

        closeAll(panel);

        gsap.killTweensOf(panel);
        panel.style.display = 'block';
        gsap.fromTo(
            panel,
            { autoAlpha: 0, y: -8, scale: 0.985 },
            {
                autoAlpha: 1,
                y: 0,
                scale: 1,
                duration: 0.22,
                ease: 'power2.out',
            }
        );
    };

    items.forEach((item) => {
        const panel = item.querySelector('[data-nav-panel]');

        if (!panel) {
            return;
        }

        gsap.set(panel, { autoAlpha: 0, y: -8, scale: 0.985, display: 'none' });

        item.addEventListener('mouseenter', () => {
            clearCloseTimer();
            openPanel(panel);
        });

        item.addEventListener('mouseleave', () => {
            clearCloseTimer();
            closeTimer = setTimeout(() => {
                closePanel(panel);
            }, 120);
        });

        item.addEventListener('focusin', () => {
            clearCloseTimer();
            openPanel(panel);
        });

        item.addEventListener('focusout', () => {
            clearCloseTimer();
            closeTimer = setTimeout(() => {
                closePanel(panel);
            }, 120);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAll();
        }
    });
}
