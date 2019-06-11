<template>
  <v-card>
    <v-navigation-drawer
      :disable-resize-watcher="true"
      floating
      permanent
      stateless
      value="true"
    >
      <v-list dense>
        <template v-for="(item, index) in items">
          <v-list-tile
            v-if="item.title"
            :key="item.title"
            :class="(item.url === $route.path) ? 'active-nav' : ''"
            @click="loadView(item.url)"
          >
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
              <v-list-tile-title>{{ item.title }}</v-list-tile-title>
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
  </v-card>
</template>

<script>
export default {
  data () {
    return {
      items: [
        { header: 'Images' },
        { title: 'Manage Image Sets', url: '/scientist/image-sets/create', icon: 'cloud_upload' },
        { divider: true },
        { header: 'Experiments' },
        { title: 'Create Experiment', url: '/scientist/experiment/create', icon: 'add_box' },
        { title: 'View Experiments', url: '/scientist/experiment/experiments', icon: 'folder' },
        { title: 'Import Experiment', url: '/scientist/experiment/import', icon: 'import_export' },
        { divider: true },
        { header: 'Other' },
        { title: 'Invite Scientist', url: '/scientist/invite', icon: 'person_add' }
      ]
    }
  },

  methods: {
    loadView (url) {
      this.$router.push(url)
    }
  }
}
</script>

<style>
.active-nav {
  background-color: #1BA1E2;
  color: #fff;
}
</style>
