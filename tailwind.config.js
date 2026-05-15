/** @type {import('tailwindcss').Config} */
export default {
  content: ["./**/*.{html,js,php}"],
  theme: {
    extend: {
      colors: {
        'farm-green': {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
        },
        'farm-olive': {
          50: '#f8faf3',
          100: '#e8f0e0',
          200: '#d1e0c2',
          300: '#b3cc9a',
          400: '#94b372',
          500: '#7a9c4d',
          600: '#5f7c3a',
          700: '#4a612c',
          800: '#36461f',
          900: '#222e13',
        },
      },
      backdropBlur: {
        xs: '2px',
      },
    },
  },
  plugins: [],
}