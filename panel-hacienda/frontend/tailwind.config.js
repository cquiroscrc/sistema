/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'sans-serif'], mono: ['JetBrains Mono', 'ui-monospace', 'Consolas', 'monospace'] },
      colors: {
        border: 'hsl(var(--border) / <alpha-value>)', input: 'hsl(var(--input) / <alpha-value>)', ring: 'hsl(var(--ring) / <alpha-value>)',
        background: 'hsl(var(--background) / <alpha-value>)', foreground: 'hsl(var(--foreground) / <alpha-value>)',
        primary: { DEFAULT: 'hsl(var(--primary) / <alpha-value>)', foreground: 'hsl(var(--primary-foreground) / <alpha-value>)' },
        muted: { DEFAULT: 'hsl(var(--muted) / <alpha-value>)', foreground: 'hsl(var(--muted-foreground) / <alpha-value>)' },
        card: { DEFAULT: 'hsl(var(--card) / <alpha-value>)', foreground: 'hsl(var(--card-foreground) / <alpha-value>)' },
        sidebar: { DEFAULT: 'hsl(var(--sidebar) / <alpha-value>)', foreground: 'hsl(var(--sidebar-foreground) / <alpha-value>)', accent: 'hsl(var(--sidebar-accent) / <alpha-value>)' },
        success: 'hsl(var(--success) / <alpha-value>)', danger: 'hsl(var(--danger) / <alpha-value>)', warning: 'hsl(var(--warning) / <alpha-value>)', info: 'hsl(var(--info) / <alpha-value>)',
      },
      borderRadius: { lg: '0.75rem', md: '0.5rem', sm: '0.375rem' },
      boxShadow: { soft: '0 1px 2px rgb(16 24 40 / .04), 0 4px 16px -4px rgb(16 24 40 / .08)' },
    },
  },
  plugins: [],
};
