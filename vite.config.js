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
      include: "resources/js/react/**/*.jsx",
      react: {
        runtime: "automatic",
      },
      input: [
        "resources/js/react/master-agent.jsx",
        // "resources/js/react/users-list.jsx",
        "resources/js/main.jsx",
      ]
    }),
    laravel({
      input: [
        "resources/sass/app.scss",
        "public/css/chat.scss",
        "public/css/app-sub.scss",
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/js/play-vue.js",
        "resources/js/fight-vue.js",
        "resources/js/ghost-vue.js",
        "resources/js/notif-vue.js",
        "public/js/play.js",
        "public/js/fight.js",
        "public/js/ghost.js",
        "resources/js/react/master-agent.jsx",
        // "resources/js/react/users-list.jsx",
        "resources/js/main.jsx",
      ],
      refresh: true,
    }),
    // reactRefresh()
  ],

});
