import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ink: '#7d3f2a',
                stone: '#b07d4f',
                paper: '#ffffff',
                sand: '#faf3e5',
                line: '#eadcc7',
            },
            letterSpacing: {
                widest2: '0.15em',
                widest3: '0.24em',
            },
            fontSize: {
                logo: ['1.4rem', { letterSpacing: '0.3em' }],
            },
        },
    },

    plugins: [forms],
};
