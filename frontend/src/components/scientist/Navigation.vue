<template>
  <div class="qe-nav-drawer">
    <v-list
      dense
      style="margin-top: 10px;"
      class="qe-drawer-1"
    >
      <v-list-item-group v-model="active" color="primary">
        <v-list-item
          v-for="(item, i) in items"
          :key="i"
          link
          @click="$router.push(item.url)"
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </div>
</template>

<script>
export default {
  data () {
    return {
      items: [
        { title: 'Dashboard', url: '/scientist/dashboard', icon: 'mdi-view-dashboard-outline' },
        { title: 'Your Image Sets', url: '/scientist/image-sets', icon: 'mdi-tooltip-image-outline' },
        { title: 'Your Experiments', url: '/scientist/experiments', icon: 'mdi-picture-in-picture-top-right-outline' }
        // picture-in-picture-top-right-outline
        // form-select
        // lightbulb-on-outline
      ],
      active: null,
      mini: true
    }
  },

  created () {
    if (this.$route.path.split('/')[2] === 'experiments') {
      this.active = 2
    } else if (this.$route.path.split('/')[2] === 'image-sets') {
      this.active = 1
    } else {
      this.active = 0
    }
  }
}
</script>

<style scoped lang="css">
  .qe-nav-drawer {
    z-index: 1;
    padding-top: 64px;
    position: fixed;
    width: 240px;
    overflow-y: auto;
    top: 0;
    bottom: 0;
    left: 0;
    border-right: 1px solid #ddd;
    background: #fff;
  }
  @media (max-width: 1150px) {
    .qe-nav-drawer {
      width: 58px;
    }
  }
</style>
