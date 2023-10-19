<template>
  <div>
    <button class="nav-link" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="far fa-bell"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right hide" style="left: inherit; right: 0px;">
      <span class="dropdown-item dropdown-header">{{ unread_count }} Notifications</span>
      <div class="dropdown-divider"></div>
      <a @click='triggerAlert()' id="allow-notifications" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
  </div>
</template>

<script>
import { reactive, ref } from 'vue';
export default {
  setup() {
    return {
      unread_count: ref(0),
      user_id: ref(0),
    }
  },
  mounted() {
    fetch('/fight/current')
      .then(resp => resp.json())
      .then(json => {
        this.user_id = json.id
      })
      .then(() => {
        window.Echo.private('cashin.' + this.user_id)
          .listen('CashIn', (e) => {
            this.unread_count++;
            this.triggerAlert();
            // this.testAlert()
          });
      })

  },
  methods: {
    testAlert() {
      navigator.serviceWorker.register('/js/ws.js');
      Notification.requestPermission(function (result) {
        if (result === 'granted') {
          navigator.serviceWorker.ready.then(function (registration) {
            registration.showNotification('Notification with ServiceWorker');
          });
        }
      });
    },
    triggerAlert() {
      navigator.serviceWorker.register('/js/ws.js');
      Notification.requestPermission(function (result) {
        if (result === 'granted') {
          navigator.serviceWorker.ready.then(function (registration) {
            registration.showNotification('New Cash-in Received!');
          });
        }
      });

      if (!("Notification" in window)) {
        // Check if the browser supports notifications
        alert("This browser does not support desktop notification");
      } else if (Notification.permission === "granted") {
        // Check whether notification permissions have already been granted;
        // if so, create a notification
        new Notification("New Cash-in Received!");
        // …
      } else if (Notification.permission !== "denied") {
        // We need to ask the user for permission
        Notification.requestPermission().then((permission) => {
          // If the user accepts, let's create a notification
          if (permission === "granted") {
            Notification("New Cash-in Received!");
            // …
          }
        });
      }

    }
  },
}
</script>

<style>
@media (max-width:767.98px) {
  #site-name {
    display: none;
  }

  .logo-container {
    align-content: center;
  }

  #notif-nav {
    display: block !important;
  }

}
</style>
