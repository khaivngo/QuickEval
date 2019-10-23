<template>
  <v-navigation-drawer
    :disable-resize-watcher="true"
    floating
    permanent
    stateless
    value="true"
    class="qe-nav-drawer"
    style="width: 200px;"
  >
    <v-list dense>
      <template v-for="(item, index) in items">
        <v-list-tile
          v-if="item.title"
          :key="item.title"
          :class="
            (parentPage(item.url) === parentPage($route.path)) ||
            ($route.path === '/scientist' && item.url === '/scientist/dashboard') ?
            'active-nav' : ''
          "
          @click="$router.push(item.url)"
        >
          <v-list-tile-content>
            <v-list-tile-title class="body-1">
              {{ item.title }}
            </v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-subheader
          v-else-if="item.header"
          :key="item.header"
        >
          {{ item.header }}
        </v-subheader>

        <v-divider
          v-else-if="item.divider"
          :key="index"
        ></v-divider>
      </template>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
export default {
  data () {
    return {
      items: [
        { title: 'Dashboard', url: '/scientist/dashboard', icon: 'insert_chart' },
        { title: 'Your Image Sets', url: '/scientist/image-sets/create', icon: 'photo' },
        { title: 'Your Experiments', url: '/scientist/experiments', icon: 'folder' }
      ]
    }
  },

  methods: {
    parentPage (url) {
      return url.split('/')[2]
    }
  }
}
</script>

<style scoped>
.qe-nav-drawer {
  background-color: rgb(250, 250, 250);
}

.active-nav {
  font-weight: 900;
}

.active-nav .v-list__tile__title {
  font-weight: 900;
}

.v-btn--active {
  background-color: #FAFAFA;
  font-weight: 900;
}
</style>
