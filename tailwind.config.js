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
            colors: {
                bg: '#F5F4F0',
                surface: '#FFFFFF',
                ink: '#0E0E0C',
                ink2: '#5A5A56',
                ink3: '#9A9A94',
                rule: '#E0DFD8',
                accent: '#1A43E0',
                'accent-bg': '#EEF1FF',
                success: '#1B7A3E',
                'success-bg': '#E8F5EE',
                warn: '#96650A',
                'warn-bg': '#FFF8E6',
                primary: '#1a42e0',
                'background-light': '#F5F4F0',
                'background-dark': '#111421',
                'neutral-border': '#E0DFD8',
                'neutral-text': '#5A5A56',
                'brand-black': '#0E0E0C',
            },
            fontFamily: {
                display: ['Lexend', 'Syne', 'Poppins', ...defaultTheme.fontFamily.sans],
                sans: ['Lexend', 'Poppins', 'DM Sans', ...defaultTheme.fontFamily.sans],
                body: ['DM Sans', 'sans-serif'],
            },
            borderRadius: {
                card: '6px',
                tag: '4px',
                pill: '9999px',
                custom: '6px',
            },
        },
    },

    plugins: [forms],
};
