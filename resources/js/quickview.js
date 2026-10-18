/**
 * Storefront "quick view": clicking a product image never navigates away.
 * The clicked <img> is cloned into a fixed-position "flyer" element that
 * flies from its grid position into the quick-view panel.
 *
 * The flyer's box (top/left/width/height) is set exactly once, synchronously,
 * to the *origin* rect the instant the user clicks — so its very first paint
 * is a pixel-perfect, invisible swap over the clicked thumbnail. From then on
 * only `transform` ever changes (never the box geometry again), which is why
 * there is no flash or jump: a single compositor-only property animates for
 * the whole flight, driven by the Web Animations API. Product details are
 * fetched in the background and revealed as soon as they arrive.
 */
export function initQuickView() {
    const modal = document.getElementById('quick-view');

    if (! modal) {
        return;
    }

    const imageSlot = modal.querySelector('[data-qv-image-slot]');
    const flyer = document.getElementById('quick-view-flyer');
    const store = () => window.Alpine.store('quickView');
    const FLIGHT_MS = 950;

    function openFromLink(link) {
        if (store().open) {
            return;
        }

        const img = link.querySelector('img');

        if (! img) {
            window.location.href = link.href;
            return;
        }

        const originRect = img.getBoundingClientRect();

        flyer.getAnimations().forEach((a) => a.cancel());
        flyer.src = img.currentSrc || img.src;
        Object.assign(flyer.style, {
            display: 'block',
            position: 'fixed',
            top: `${originRect.top}px`,
            left: `${originRect.left}px`,
            width: `${originRect.width}px`,
            height: `${originRect.height}px`,
            margin: '0',
            zIndex: '200',
            transform: 'none',
            transformOrigin: 'top left',
            boxShadow: '0 0 0 rgba(17,17,17,0)',
        });

        store().product = null;
        store().loading = true;
        store().imageLanded = false;
        store().open = true;
        document.body.style.overflow = 'hidden';

        let landed = false;
        const land = () => {
            if (landed) {
                return;
            }
            landed = true;
            store().imageLanded = true;
            flyer.style.display = 'none';
        };

        // Two frames: the first lets Alpine actually paint the (now open)
        // panel with a stable layout so the target rect we read is the real
        // resting position — not a still-fading-in one. The panel itself
        // only fades (no scale/translate) for exactly this reason.
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                const targetRect = imageSlot.getBoundingClientRect();

                const dx = targetRect.left - originRect.left;
                const dy = targetRect.top - originRect.top;
                const scaleX = targetRect.width / originRect.width;
                const scaleY = targetRect.height / originRect.height;

                // A hair short of the final spot, then settle in — a soft
                // "landing" instead of a hard stop.
                const nearlyThere = `translate(${dx * 0.98}px, ${dy * 0.98}px) scale(${scaleX - (scaleX - 1) * 0.02}, ${scaleY - (scaleY - 1) * 0.02})`;
                const end = `translate(${dx}px, ${dy}px) scale(${scaleX}, ${scaleY})`;

                const animation = flyer.animate(
                    [
                        { transform: 'translate(0px, 0px) scale(1, 1)', boxShadow: '0 0 0 rgba(17,17,17,0)' },
                        { transform: nearlyThere, boxShadow: '0 30px 70px rgba(17,17,17,0.25)', offset: 0.9 },
                        { transform: end, boxShadow: '0 20px 45px rgba(17,17,17,0.16)' },
                    ],
                    {
                        duration: FLIGHT_MS,
                        easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                        fill: 'forwards',
                    }
                );

                animation.finished.then(land).catch(() => {});
            });
        });

        // Safety net: guarantees the panel reveals even if the animation
        // promise never resolves (e.g. reduced-motion, tab backgrounded).
        setTimeout(land, FLIGHT_MS + 300);

        fetch(link.dataset.quickViewUrl, { headers: { Accept: 'application/json' } })
            .then((response) => response.json())
            .then((data) => {
                store().product = data;
                store().loading = false;
            })
            .catch(() => {
                close();
                window.location.href = link.href;
            });
    }

    function close() {
        store().open = false;
        store().imageLanded = false;
        document.body.style.overflow = '';
        flyer.getAnimations().forEach((a) => a.cancel());
        flyer.style.display = 'none';
    }

    document.querySelectorAll('[data-quick-view]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.button === 1) {
                return;
            }

            event.preventDefault();
            openFromLink(link);
        });
    });

    modal.addEventListener('click', (event) => {
        if (event.target.closest('[data-qv-close]') || event.target.hasAttribute('data-qv-backdrop')) {
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
