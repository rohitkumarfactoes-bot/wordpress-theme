/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './inc/**/*.php',
    './template-parts/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: 'var(--color-primary)',
        'primary-dark': 'var(--color-primary-dark)',
        text: 'var(--color-text)',
        'text-light': 'var(--color-text-light)',
        'text-lighter': 'var(--color-text-lighter)',
        bg: 'var(--color-bg)',
        'bg-alt': 'var(--color-bg-alt)',
        'bg-dark': 'var(--color-bg-dark)',
        border: 'var(--color-border)',
        'border-dark': 'var(--color-border-dark)',
      },
      fontFamily: {
        primary: 'var(--font-primary)',
        heading: 'var(--font-heading)',
      },
      maxWidth: {
        container: 'var(--container-width)',
      }
    },
  },
  plugins: [],
}