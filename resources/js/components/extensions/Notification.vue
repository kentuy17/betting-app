<template>
  <div>
    <button class="nav-link icon-bell" @click='markAllAsRead()' role="button" data-bs-toggle="dropdown"
      aria-haspopup="true" aria-expanded="false">
      <i class="far fa-bell"></i>
      <span class="badge bg-danger text-xs px-1 py-0 mr-0" v-if="unread_count > 0">{{ unread_count }}</span>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right hide max-h-80 overflow-scroll"
      style="left: inherit; right: 0px;">
      <span class="dropdown-item dropdown-header">{{ unread_count ?? 0 }} Notifications</span>
      <!-- <div class="dropdown-divider"></div> -->
      <div v-show="users.length > 0" v-for='(user, index) in users' :key='index'>
        <a href="#" @click='redirectTrans()' class="dropdown-item">
          <i class="fas fa-envelope mr-2"></i> {{ user }} cash-in <span class="float-right text-muted text-sm">3
            mins</span>
        </a>
        <div class="dropdown-divider"></div>
      </div>
      <a @click='readAllNotif()' id="allow-notifications" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
export default {
  setup() {
    return {
      unread_count: ref(0),
      user_id: ref(0),
      users: [],
      legit: true
    }
  },
  async mounted() {
    // this.unread_count = localStorage.getItem('unread')
    fetch('/user/info')
      .then(resp => resp.json())
      .then(json => {
        let user = json.data
        this.user_id = user.id
        this.legit = user.legit

        if (!this.legit) {
          this.users = ['adoy', 'lao2x', 'etong']
        }
      })
      .then(() => {
        if (localStorage.getItem('unread') == null && !this.legit) {
          this.unread_count = this.users.length
        } else {
          this.unread_count = localStorage.getItem('unread')
        }
      })
      .catch(err => console.log(err))

    window.socket.on('notify-deposit', (player) => {
      this.delay(5000).then(() => {
        this.users.push(player)
        this.unread_count = parseInt(this.unread_count) + 1
      })
    })
  },
  methods: {
    delay(time) {
      return new Promise(resolve => setTimeout(resolve, time));
    },
    redirectTrans() {
      if (this.unread_count > 0) {
        this.unread_count -= 1
        localStorage.setItem('unread', this.unread_count)
      }
      window.location.href = '/transactions'
    },
    readAllNotif() {
      localStorage.setItem('unread', 0)
      this.unread_count = 0
      window.location.href = '/transactions'
    },
    markAllAsRead() {
      localStorage.setItem('unread', 0)
      this.unread_count = 0
    },
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

  .icon-bell {
    position: relative;
  }

  .icon-bell span {
    position: absolute;
    top: -2px;
    right: -8px;
    display: block;
  }


}
</style>
