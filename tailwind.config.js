/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    safelist: [
        {
            pattern: /^(bg|text|border|from|to|ring)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/,
        },
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: 'rgb(var(--tw-brand-50) / <alpha-value>)',
                    100: 'rgb(var(--tw-brand-100) / <alpha-value>)',
                    200: 'rgb(var(--tw-brand-200) / <alpha-value>)',
                    300: 'rgb(var(--tw-brand-300) / <alpha-value>)',
                    400: 'rgb(var(--tw-brand-400) / <alpha-value>)',
                    500: 'rgb(var(--tw-brand-500) / <alpha-value>)',
                    600: 'rgb(var(--tw-brand-600) / <alpha-value>)',
                    700: 'rgb(var(--tw-brand-700) / <alpha-value>)',
                },
                secondary: {
                    50: 'rgb(var(--tw-secondary-50) / <alpha-value>)',
                    100: 'rgb(var(--tw-secondary-100) / <alpha-value>)',
                    200: 'rgb(var(--tw-secondary-200) / <alpha-value>)',
                    300: 'rgb(var(--tw-secondary-300) / <alpha-value>)',
                    400: 'rgb(var(--tw-secondary-400) / <alpha-value>)',
                    500: 'rgb(var(--tw-secondary-500) / <alpha-value>)',
                    600: 'rgb(var(--tw-secondary-600) / <alpha-value>)',
                    700: 'rgb(var(--tw-secondary-700) / <alpha-value>)',
                },
                accent: {
                    300: 'rgb(var(--tw-accent-300) / <alpha-value>)',
                    400: 'rgb(var(--tw-accent-400) / <alpha-value>)',
                    500: 'rgb(var(--tw-accent-500) / <alpha-value>)',
                },
                ink: 'rgb(var(--tw-ink) / <alpha-value>)',
                muted: 'rgb(var(--tw-muted) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['Outfit', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                soft: '0 12px 40px -18px rgba(15, 23, 42, 0.25)',
            },
        },
    },
    plugins: [],
};
