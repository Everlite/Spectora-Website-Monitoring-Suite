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
                sans: ['Inter', 'Outfit', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                spectora: {
                    obsidian: '#070B12',
                    bg: '#0B0F17',
                    surface: '#111827',
                    card: '#131B2E',
                    'card-elevated': '#182238',
                    border: '#1E293B',
                    'border-highlight': '#334155',
                    cyan: '#00F2FE',
                    'cyan-glow': 'rgba(0, 242, 254, 0.4)',
                    emerald: '#10B981',
                    'emerald-glow': 'rgba(16, 185, 129, 0.4)',
                    violet: '#8B5CF6',
                    'violet-glow': 'rgba(139, 92, 246, 0.4)',
                    rose: '#F43F5E',
                    'rose-glow': 'rgba(244, 63, 94, 0.4)',
                    amber: '#F59E0B',
                }
            },
            boxShadow: {
                'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
                'neon-cyan': '0 0 20px -3px rgba(0, 242, 254, 0.35)',
                'neon-emerald': '0 0 20px -3px rgba(16, 185, 129, 0.35)',
                'neon-violet': '0 0 20px -3px rgba(139, 92, 246, 0.35)',
                'neon-rose': '0 0 20px -3px rgba(244, 63, 94, 0.35)',
            },
            backdropBlur: {
                'xs': '2px',
            }
        },
    },

    plugins: [forms],
};
