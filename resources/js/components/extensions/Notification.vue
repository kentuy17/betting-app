<template>
  <div>
    <a class="nav-link" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
      <i class="far fa-bell"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right hide" style="left: inherit; right: 0px;">
      <span class="dropdown-item dropdown-header">{{ unread_count }} Notifications</span>
      <div class="dropdown-divider"></div>
      <a href="#" id="allow-notifications" class="dropdown-item dropdown-footer">See All Notifications</a>
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
            console.log(e.cashin);
            this.triggerAlert();
          });
      })

  },
  methods: {
    triggerAlert() {
      if (!("Notification" in window)) {
        // Check if the browser supports notifications
        alert("This browser does not support desktop notification");
      } else if (Notification.permission === "granted") {
        // Check whether notification permissions have already been granted;
        // if so, create a notification
        const notification = new Notification("Hi there!");
        // …
      } else if (Notification.permission !== "denied") {
        // We need to ask the user for permission
        Notification.requestPermission().then((permission) => {
          // If the user accepts, let's create a notification
          if (permission === "granted") {
            const notification = new Notification("Hi there!");
            // …
          }
        });
      }

    }
  },
}
</script>

<style>
  @media (max-width:767.98px){
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
