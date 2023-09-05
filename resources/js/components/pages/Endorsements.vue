<template>
</template>

<script>
import resumeSidemenu from "./resumeSidemenu";

export default {
  name: "endorsements",
  data() {
    return {     
    };
  },
  components: {
    resumeSidemenu
  },
  mounted(){
    localStorage.setItem('page',9)
  },
};
</script>