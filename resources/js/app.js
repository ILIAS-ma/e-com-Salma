import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import { initNavbarGsap } from './navbar-gsap';
import { initQuickView } from './quickview';

window.Alpine = Alpine;

Alpine.plugin(intersect);

document.addEventListener('alpine:init', () => {
    Alpine.store('quickView', {
        open: false,
        loading: false,
        imageLanded: false,
        product: null,
    });

    Alpine.store('cart', {
        count: Number(document.body.dataset.cartCount || 0),
    });
});

Alpine.start();

document.addEventListener('DOMContentLoaded', initQuickView);
document.addEventListener('DOMContentLoaded', initNavbarGsap);
