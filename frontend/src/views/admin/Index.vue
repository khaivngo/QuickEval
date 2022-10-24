<template>
  <div class="d-flex">
    <Navigation/>
    <!--
      the nav menu above is position fixed, so we put a "mold" below,
      making sure the column to the right does not go beneath it
    -->
    <div class="mold"></div>

    <router-view/>
  </div>
</template>

<script>
import Navigation from '@/components/admin/Navigation'

export default {
  components: {
    Navigation
  }
}
</script>

<style scoped lang="css">
  .mold {
    flex: 0 0 240px;
    height: 20px;
  }
  @media (max-width: 1150px) {
    .mold {
      flex: 0 0 58px;
    }
  }
</style>
