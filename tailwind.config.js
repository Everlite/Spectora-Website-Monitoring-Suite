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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Ultra-Premium Matte Slate & Carbon Palette (Linear/Stripe inspired)
                slate: {
                    850: '#141D2E',
                    925: '#0B1120',
                    950: '#070C18',
                },
                spectora: {
                    bg: '#090D16',          // Deep matte carbon background
                    surface: '#0F1626',     // Card surface
                    elevated: '#151F33',    // Elevated elements & modals
                    border: '#1E293B',      // Subtle clean border
                    'border-hover': '#334155', // Border on hover
                    primary: '#2563EB',     // Sophisticated Blue
                    'primary-hover': '#1D4ED8',
                    emerald: '#059669',     // Clean muted emerald
                    rose: '#E11D48',        // Clean muted rose
                    amber: '#D97706',       // Clean muted amber
                }
            },
            boxShadow: {
                'subtle': '0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px -1px rgba(0, 0, 0, 0.3)',
                'card': '0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3)',
                'modal': '0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5)',
            }
        },
    },

    plugins: [forms],
};
