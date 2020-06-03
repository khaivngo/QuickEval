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
      <!-- <v-subheader>REPORTS</v-subheader> -->
      <v-list-item-group color="primary">
        <v-list-item
          v-for="(item, i) in items"
          :key="i"
          :class="
            (parentPage(item.url) === parentPage($route.path)) || ($route.path === '/scientist' && item.url === '/scientist/dashboard') ?
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
