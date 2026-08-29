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
                sans: ['Plus Jakarta Sans', 'DM Sans', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Horizon UI Official Palette
                navy: {
                    50: '#E9EBF8',
                    100: '#C7CEEF',
                    200: '#A3AED0', // Subtitle text
                    300: '#707EAE', // Secondary text
                    400: '#434E7B',
                    500: '#2B3674', // Borders & separators
                    600: '#1B254B', // Secondary card / elevated
                    700: '#111C44', // Main Card background
                    800: '#0B1437', // Main Body background
                    900: '#080F27',
                },
                brand: {
                    100: '#E9E3FF',
                    200: '#C0B3FF',
                    300: '#9C86FF',
                    400: '#868CFF',
                    500: '#7551FF', // Primary Horizon Purple / Indigo
                    600: '#4318FF', // Deep Indigo
                    700: '#3311CC',
                    800: '#250B99',
                    900: '#180566',
                },
                horizon: {
                    green: '#01B574', // Horizon Success Emerald
                    'green-light': '#05CD99',
                    red: '#EE5D50',   // Horizon Danger Coral
                    amber: '#FFB547', // Horizon Warning Golden
                    blue: '#3965FF',
                }
            },
            borderRadius: {
                'horizon': '20px',
                'horizon-lg': '24px',
                'horizon-sm': '14px',
            },
            boxShadow: {
                'horizon': '0px 18px 40px rgba(112, 144, 176, 0.08)',
                'horizon-card': '0px 18px 40px rgba(0, 0, 0, 0.25)',
                'horizon-btn': '0px 4px 10px rgba(117, 81, 255, 0.3)',
            }
        },
    },

    plugins: [forms],
};
