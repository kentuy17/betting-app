import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import react from '@vitejs/plugin-react';

export default defineConfig({
  server: {
    watch: { usePolling: true },
  },
  plugins: [
    vue(),
    react(),
    splitVendorChunkPlugin(),
    // react({
    // include: 'resources/js/react/**/*.jsx',
    // react: {
    //   runtime: 'automatic',
    // },
    // input: [
    //   'resources/js/react/master-agent.jsx',
    //   'resources/js/react/users-list.jsx',
    //   'resources/js/main.jsx',
    // ],
    // }),
    laravel({
      input: [
        'resources/sass/app.scss',
        'public/css/chat.scss',
        'public/css/app-sub.scss',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/play-vue.js',
        'resources/js/fight-vue.js',
        'resources/js/ghost-vue.js',
        'resources/js/notif-vue.js',
        'public/js/play.js',
        'public/js/fight.js',
        'public/js/ghost.js',
        'resources/js/react/master-agent.jsx',
        // 'resources/js/react/pages/users-list.jsx',
        'resources/js/main.jsx',
        'public/js/topups.js',
        'public/js/transactions.js',
        'public/js/withdraw.js',
      ],
      refresh: true,
    }),
    // reactRefresh()
  ],
  build: {
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            return id
              .toString()
              .split('node_modules/')[1]
              .split('/')[0]
              .toString();
          }
        },
      },
    },
  },
});
