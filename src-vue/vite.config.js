import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
  plugins: [tailwindcss(), vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  build: {
    outDir: '../assets/dist',
    emptyOutDir: true,
    rollupOptions: {
      input: './src/main.js',
      output: {
        // No hashes in filenames so PHP can always reference them by fixed path
        entryFileNames: 'index.js',
        assetFileNames: (info) => info.name?.endsWith('.css') ? 'index.css' : '[name].[ext]'
      }
    }
  }
});
