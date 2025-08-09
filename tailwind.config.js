import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
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
