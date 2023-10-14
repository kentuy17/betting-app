import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import react from "@vitejs/plugin-react";

export default defineConfig({
  server: {
    watch: { usePolling: true }
  },
  plugins: [
    vue(),
    react({
      input: [
        'resources/react/master-agent.jsx'
      ],
    }),
    laravel({
      input: [
        "resources/sass/app.scss",
        "public/css/chat.scss",
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/js/play-vue.js",
        "resources/js/fight-vue.js",
        "resources/js/ghost-vue.js",
        "resources/js/notif-vue.js",
        "public/js/play.js",
        "public/js/fight.js",
        "public/js/ghost.js",
      ],
      refresh: true,
    }),
    // reactRefresh()
  ],

});
