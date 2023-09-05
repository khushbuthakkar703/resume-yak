<template>
</template>

<script>
import resumeSidemenu from "./resumeSidemenu";

export default {
  name: "job_descriptions",
  data() {
    return {     
    };
  },
  components: {
    resumeSidemenu
  },
  mounted(){
    localStorage.setItem('page',7);
    $(".my_resumes").removeClass("active_nav");
    $(".has_sub_nav_my_profile").removeClass("active_nav");
  },
};
</script>