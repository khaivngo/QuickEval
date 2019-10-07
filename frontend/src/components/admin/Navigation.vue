<template>
  <v-navigation-drawer
    :disable-resize-watcher="true"
    floating
    permanent
    stateless
    value="true"
    style="background-color: rgb(250, 250, 250);"
  >
    <v-list dense>
      <template v-for="(item, index) in items">
        <v-list-tile
          v-if="item.title"
          :key="item.title"
          :class="(item.url === $route.path) ? 'active-nav' : ''"
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
        // { title: 'Dashboard', url: '/admin/dashboard', icon: 'insert_chart' },
        { title: 'Scientist Requests', url: '/admin/scientist-role-requests', icon: 'verified_user' }
      ]
    }
  }
}
</script>

<style scoped>
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
