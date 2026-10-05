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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                kopi: {
                    DEFAULT: '#A56A2A',
                    dark: '#6F4A26',
                    light: '#F8F5EF',
                    accent: '#E79A52',
                    muted: '#F3F4F6',
                },
            },
            boxShadow: {
                kopi: '0 15px 45px rgba(111, 74, 38, 0.12)',
            },
        },
    },

    plugins: [forms],
};
