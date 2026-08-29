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
                sans: ['Plus Jakarta Sans', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                studio: {
                    bg: '#090B10',          // Deep Canvas
                    surface: '#111622',     // Primary Card
                    elevated: '#171E2E',    // Secondary Card / Inset
                    hover: '#1D2538',       // Active Hover
                    border: '#202A3E',      // Primary Border
                    'border-subtle': '#182030',
                    brand: '#3B57E8',       // Studio Cobalt Primary
                    'brand-hover': '#4F6BFF',
                    emerald: '#10B981',     // Healthy / Online
                    rose: '#F43F5E',        // Down / Outage
                    amber: '#F59E0B',       // Degraded / Expiring
                    sky: '#0EA5E9',         // Telemetry / Pulse
                    text: '#F1F3F9',        // Primary Heading
                    muted: '#8A95A8',       // Subtitles / Labels
                    subtle: '#5A667A',      // Meta / Timestamps
                }
            },
            borderRadius: {
                'studio': '16px',
                'studio-lg': '20px',
                'studio-sm': '10px',
            },
            boxShadow: {
                'studio-sm': '0 1px 3px 0 rgba(0, 0, 0, 0.4), 0 1px 2px -1px rgba(0, 0, 0, 0.4)',
                'studio-card': '0 4px 20px -2px rgba(0, 0, 0, 0.5), 0 2px 6px -1px rgba(0, 0, 0, 0.4)',
                'studio-btn': '0 2px 10px rgba(59, 87, 232, 0.35)',
            }
        },
    },

    plugins: [forms],
};
