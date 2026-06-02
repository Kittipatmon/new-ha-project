import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
            colors: {
                kumwell: {
                    red: '#D71920', // Standard Kumwell Red
                    dark: '#121418',
                    card: '#1E2129',
                    hover: '#2A2E38'
                }
            },
            width: {
                '68': '17rem',
            },
            fontFamily: {
                sans: ['Prompt', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, require('daisyui')],

    daisyui: {
        themes: ["light", "dark"],
        darkTheme: "dark",
    },
};
