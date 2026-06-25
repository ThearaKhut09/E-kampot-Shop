import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        '!./resources/views/welcome.blade.php',
    ],

    safelist: [
        // Gradient utilities for payment success UI
        'bg-gradient-to-r', 'bg-gradient-to-br',
        'from-emerald-400', 'from-emerald-500', 'from-emerald-600', 'from-emerald-700', 'from-emerald-800',
        'to-emerald-400', 'to-emerald-500', 'to-emerald-600', 'to-emerald-700', 'to-emerald-800',
        'from-teal-500', 'from-teal-600', 'from-teal-700', 'from-teal-800',
        'to-teal-500', 'to-teal-600', 'to-teal-700', 'to-teal-800', 'to-teal-900',
        'from-green-500', 'from-green-600', 'to-green-500', 'to-green-600',
        'hover:from-emerald-800', 'hover:to-teal-900',
        // Emerald background, text, border utilities
        'bg-emerald-50', 'bg-emerald-100', 'bg-emerald-500', 'bg-emerald-600',
        'text-emerald-100', 'text-emerald-400', 'text-emerald-500', 'text-emerald-600',
        'border-emerald-200', 'border-emerald-700',
        'dark:bg-emerald-900', 'dark:text-emerald-400',
        'hover:bg-emerald-100', 'dark:hover:bg-emerald-900',
        'focus:border-emerald-500', 'focus:ring-emerald-500',
        // Teal utilities
        'bg-teal-600', 'bg-teal-700', 'bg-teal-800', 'text-teal-600',
        // Opacity variants used in checkout pages
        'via-emerald-50',
        // Animation and backdrop utilities
        'animate-bounce', 'animate-pulse', 'animate-spin',
        'backdrop-blur-xl', 'backdrop-blur-sm',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand palette
                primary: colors.indigo,
                success: colors.emerald,
                warning: colors.amber,
                danger: colors.rose,
            },
            boxShadow: {
                soft: '0 4px 20px -5px rgba(0,0,0,0.1)',
            },
            borderRadius: {
                xl: '0.9rem',
            },
        },
    },

    plugins: [forms],
};
