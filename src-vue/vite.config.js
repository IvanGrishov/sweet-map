import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [tailwindcss(), vue()],
  build: {
    // 1. Выходим из src-vue и кладем билд в assets/dist
    outDir: '../assets/dist',
    emptyOutDir: true,
    rollupOptions: {
      output: {
        // 2. Убираем хеши из названий, чтобы PHP их всегда "видел"
        entryFileNames: 'index.js',
        assetFileNames: '[name].[ext]'
      }
    }
  }
});
