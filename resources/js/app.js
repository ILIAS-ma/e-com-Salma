import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import { initQuickView } from './quickview';

window.Alpine = Alpine;

Alpine.plugin(intersect);

document.addEventListener('alpine:init', () => {
    Alpine.store('quickView', {
        open: false,
        product: null,
        previewImage: null,
        previewName: '',
    });

    Alpine.store('cart', {
        count: Number(document.body.dataset.cartCount || 0),
    });
});

Alpine.start();

document.addEventListener('DOMContentLoaded', initQuickView);
