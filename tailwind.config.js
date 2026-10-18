import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            // Neo-grotesk stack: Helvetica where available, Inter Tight otherwise.
            fontFamily: {
                sans: ['"Helvetica Neue"', 'Helvetica', '"Inter Tight"', 'Arial', ...defaultTheme.fontFamily.sans],
            },
            // Black & white, editorial. The logo red is only used for sale prices.
            colors: {
                ink: '#000000',
                stone: '#6f6f6f',
                paper: '#ffffff',
                sand: '#f2f2f2',
                line: '#e2e2e2',
                brand: '#b3261a',
            },
            letterSpacing: {
                widest2: '0.12em',
                widest3: '0.2em',
            },
            transitionTimingFunction: {
                silk: 'cubic-bezier(0.22, 1, 0.36, 1)',
            },
        },
    },

    plugins: [forms],
};
