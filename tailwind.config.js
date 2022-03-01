module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/js/*.js'
  ],
  darkMode: 'class', // or 'media' or 'class'
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
