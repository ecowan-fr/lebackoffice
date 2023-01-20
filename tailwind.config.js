/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.php",
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  darkMode: 'class',
  theme: {
    extend: {},
    fontFamily: {
      visby: 'Visby Round CF',
      marianne: 'Marianne'
    },
    screens: {
      print: { 'raw': 'print' },
      sm: '600px',
      md: '960px',
      lg: '1280px',
      xl: '1440px'
    },
  },
  plugins: [],
}
