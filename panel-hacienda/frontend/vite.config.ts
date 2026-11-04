import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Se publica en https://sistema.lospaticas.com/panel-hacienda/ (nginx sirve dist/ copiado a panel-hacienda/)
export default defineConfig({
  base: '/panel-hacienda/',
  plugins: [react()],
  build: { outDir: 'dist', assetsDir: 'assets', sourcemap: false, chunkSizeWarningLimit: 900 },
});
