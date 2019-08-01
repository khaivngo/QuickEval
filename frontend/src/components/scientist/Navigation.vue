<template>
  <!-- <v-card>
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
            @click="$router.push(item.url)"
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
  </v-card> -->

  <v-container fluid>
    <!-- <div class="subheading">
      Username
    </div> -->
    <v-layout
      wrap justify-center
      mb-3
    >
      <v-btn :to="'/scientist/dashboard'" color="grey darken-3" flat class="ma-0 text-none">
        <v-icon left>insert_chart_outlined</v-icon>
        <span class="pr-3">Dashboard</span>
      </v-btn>

      <v-btn :to="'/scientist/image-sets/create'" color="grey darken-3" flat class="ma-0 text-none">
        <v-icon left>folder_open</v-icon>
        <span class="pr-3">Manage Image Sets</span>
      </v-btn>

      <v-btn :to="'/scientist/experiment/create'" flat color="grey darken-3" class="ma-0 text-none">
        <v-icon left>add</v-icon>
        <span class="pr-3">Create Experiment</span>
      </v-btn>

      <v-btn :to="'/scientist/experiment/experiments'" flat color="grey darken-3" class="ma-0 text-none">
        <v-icon left>folder_open</v-icon>
        <span class="pr-3">Manage Experiments</span>
      </v-btn>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  data () {
    return {
      items: [
        { title: 'Dashboard', url: '/scientist/dashboard', icon: 'insert_chart' },
        { header: 'Images' },
        { title: 'Manage Image Sets', url: '/scientist/image-sets/create', icon: 'cloud_upload' },
        { divider: true },
        { header: 'Experiments' },
        { title: 'Create Experiment', url: '/scientist/experiment/create', icon: 'add_box' },
        { title: 'Manage Experiments', url: '/scientist/experiment/experiments', icon: 'folder' }
        // { title: 'Import Experiment', url: '/scientist/experiment/import', icon: 'import_export' },
        // { divider: true },
        // { header: 'Other' },
        // { title: 'Invite Scientist', url: '/scientist/invite', icon: 'person_add' }
      ]
    }
  }
}
</script>

<style scoped>
.active-nav {
  background-color: #1BA1E2;
  color: #fff;
}

.v-btn--active {
  background-color: #FAFAFA;
  font-weight: 900;
}
</style>
