<template>
  <div>
    <a class="nav-link" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
      <i class="far fa-bell"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right hide" style="left: inherit; right: 0px;">
      <span class="dropdown-item dropdown-header">0 Notifications</span>
      <div class="dropdown-divider"></div>
      <a href="#" id="allow-notifications" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
  </div>
</template>

<script>
export default {
  setup() {

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
