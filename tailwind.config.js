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
                sans: ['Source Sans 3', 'Helvetica Neue', ...defaultTheme.fontFamily.sans],
                display: ['Instrument Serif', 'Georgia', 'serif'],
                mono: ['IBM Plex Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                studio: {
                    bg: '#0C0A08',
                    surface: '#14110E',
                    elevated: '#1C1814',
                    hover: '#241F19',
                    border: '#2A241C',
                    'border-subtle': '#1A1612',
                    brand: '#E4F54A',
                    'brand-hover': '#F2FF7A',
                    emerald: '#7DDB8A',
                    rose: '#E85D4C',
                    amber: '#E8A14A',
                    sky: '#6EC8E8',
                    text: '#F3EDE3',
                    muted: '#9C9488',
                    subtle: '#6B645A',
                },
            },
            borderRadius: {
                studio: '0',
                'studio-lg': '0',
                'studio-sm': '0',
            },
            boxShadow: {
                'studio-sm': 'none',
                'studio-card': 'none',
                'studio-btn': 'none',
            },
        },
    },

    plugins: [forms],
};
