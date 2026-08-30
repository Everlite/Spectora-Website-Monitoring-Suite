import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Roboto', ...defaultTheme.fontFamily.sans],
                display: ['Roboto', ...defaultTheme.fontFamily.sans],
                mono: ['Roboto Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                studio: {
                    bg: '#f0f2f5',
                    surface: '#ffffff',
                    elevated: '#f8f9fa',
                    hover: '#e8f0fe',
                    border: '#dadce0',
                    'border-subtle': '#e8eaed',
                    brand: '#1a73e8',
                    'brand-hover': '#1765cc',
                    emerald: '#188038',
                    rose: '#d93025',
                    amber: '#e37400',
                    sky: '#1a73e8',
                    text: '#202124',
                    muted: '#5f6368',
                    subtle: '#80868b',
                },
            },
            borderRadius: {
                studio: '8px',
                'studio-lg': '12px',
                'studio-sm': '4px',
            },
            boxShadow: {
                'studio-sm': '0 1px 2px 0 rgba(60, 64, 67, 0.15)',
                'studio-card': '0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15)',
                'studio-btn': '0 1px 2px 0 rgba(60, 64, 67, 0.3)',
            },
        },
    },

    plugins: [forms],
};
