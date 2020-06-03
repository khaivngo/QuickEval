<template>
  <v-card
    class="mx-auto"
    max-width="400"
    tile
    flat
  >
    <v-list
      flat
    >
      <v-list-item-group color="primary">
        <v-list-item
          v-for="(item, i) in items"
          :key="i"
          :class="
            (parentPage(item.url) === parentPage($route.path)) || ($route.path === '/admin' && item.url === '/admin/scientist-role-requests') ?
            'qe-active-nav' : ''
          "
          @click="$router.push(item.url)"
        >
          <v-list-item-content>
            <v-list-item-title v-html="item.title"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </v-card>
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
  },

  methods: {
    parentPage (url) {
      return url.split('/')[2]
    }
  }
}
</script>

<style scoped lang="css">
.qe-active-nav {
  font-weight: 900;
  /*color: rgb(25, 118, 210);*/
  color: black;
}

.v-list-item--active, .v-item--active {
  /*background-color: #FAFAFA;*/
  /*font-weight: 900;*/
  /*color: rgb(25, 118, 210);*/
}
</style>
