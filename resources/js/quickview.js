import { gsap } from 'gsap';

/**
 * "Quick add" drawer, opened from the "+" button of a product card.
 * Slides in from the right on desktop and up from the bottom on mobile.
 * The card photo is shown immediately while the product details are fetched,
 * and scroll is locked with scrollbar compensation so the page never jumps.
 */
export function initQuickView() {
    const modal = document.getElementById('quick-view');

    if (! modal) {
        return;
    }

    const backdrop = modal.querySelector('[data-qv-backdrop]');
    const panel = modal.querySelector('[data-qv-panel]');
    const store = () => window.Alpine.store('quickView');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isSheet = () => window.matchMedia('(max-width: 767px)').matches;
    const hiddenPosition = () => (isSheet() ? { xPercent: 0, yPercent: 100 } : { xPercent: 100, yPercent: 0 });

    let timeline = null;
    let lastTrigger = null;

    function lockScroll() {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = scrollbarWidth > 0 ? `${scrollbarWidth}px` : '';
    }

    function unlockScroll() {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    function open(trigger) {
        if (store().open) {
            return;
        }

        lastTrigger = trigger;
        const img = trigger.closest('[data-product-card]')?.querySelector('img');

        store().product = null;
        store().previewImage = img ? (img.currentSrc || img.src) : null;
        store().previewName = trigger.dataset.productName ?? '';
        store().open = true;
        lockScroll();

        requestAnimationFrame(() => {
            timeline?.kill();

            if (reducedMotion) {
                gsap.set(backdrop, { autoAlpha: 1 });
                gsap.set(panel, { xPercent: 0, yPercent: 0 });
                return;
            }

            timeline = gsap.timeline();
            timeline.fromTo(backdrop, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.4, ease: 'power1.out' }, 0);
            timeline.fromTo(panel, hiddenPosition(), { xPercent: 0, yPercent: 0, duration: 0.6, ease: 'expo.out' }, 0);
        });

        fetch(trigger.dataset.quickViewUrl, { headers: { Accept: 'application/json' } })
            .then((response) => response.json())
            .then((data) => {
                if (store().open) {
                    store().product = data;
                }
            })
            .catch(() => {
                close(true);
                window.location.href = trigger.dataset.productUrl;
            });
    }

    function close(immediate = false) {
        if (! store().open) {
            return;
        }

        timeline?.kill();

        const finish = () => {
            store().open = false;
            store().product = null;
            gsap.set([backdrop, panel], { clearProps: 'all' });
            unlockScroll();
            lastTrigger?.focus({ preventScroll: true });
        };

        if (immediate || reducedMotion) {
            finish();
            return;
        }

        timeline = gsap.timeline({ onComplete: finish });
        timeline.to(backdrop, { autoAlpha: 0, duration: 0.3, ease: 'power1.in' }, 0);
        timeline.to(panel, { ...hiddenPosition(), duration: 0.35, ease: 'power2.in' }, 0);
    }

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-quick-view]');

        if (trigger) {
            event.preventDefault();
            open(trigger);
            return;
        }

        if (store().open && (event.target.closest('[data-qv-close]') || event.target.hasAttribute('data-qv-backdrop'))) {
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && store().open) {
            close();
        }
    });

    window.closeQuickView = close;
}
