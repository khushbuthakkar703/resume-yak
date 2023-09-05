<template>
<li v-if="this.logout != ''" class="current-page">
  <a href="/builder/contact/#/logout">Logout </a>
</li>
</template>

<script>

export default {
  name: "logout-start",
  data() {
    return {
      logout: (localStorage.getItem('token')) ? localStorage.getItem('token') : ''    
    };
  },
  mounted() {
  },
};
</script>