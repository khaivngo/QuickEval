<template>
  <!-- <v-card> -->
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
            <!-- <v-list-tile-action style="background: red;" class="pa-0 ma-0"> -->
              <v-icon class="mr-2">{{ item.icon }}</v-icon>
            <!-- </v-list-tile-action> -->

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
  <!-- </v-card> -->

  <!-- <v-container fluid>
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
  </v-container> -->

  <!-- <v-list style="max-width: 300px; background-color: rgb(250, 250, 250);">
    <v-list-group
      v-for="item in items"
      :key="item.title"
      v-model="item.active"
      :prepend-icon="item.icon"
      no-action
    >
      <template v-slot:activator>
      <v-list-tile>
        <v-list-tile-content>
          <v-list-tile-title>{{ item.title }}</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
      </template>

      <v-list-tile
        v-for="subItem in item.items"
        :key="subItem.title"
        :class="(item.url === $route.path) ? 'active-nav' : ''"
        @click="$router.push(item.url)"
      >
        <v-list-tile-content>
          <v-list-tile-title>{{ subItem.title }}</v-list-tile-title>
        </v-list-tile-content>

        <v-list-tile-action>
          <v-icon>{{ subItem.action }}</v-icon>
        </v-list-tile-action>
      </v-list-tile>
    </v-list-group>
  </v-list> -->
</template>

<script>
export default {
  data () {
    return {
      items: [
        { title: 'Dashboard', url: '/scientist/dashboard', icon: 'insert_chart' },
        // { header: 'Images' },
        // { divider: true, inset: true },
        { title: 'Your Image Sets', url: '/scientist/image-sets/create', icon: 'photo' },
        // { divider: true, inset: true },
        // { header: 'Experiments' },
        { title: 'Your Experiments', url: '/scientist/experiment/experiments', icon: 'folder' }
        // { title: 'Create Experiment', url: '/scientist/experiment/create', icon: 'add_box' }
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
  /*background-color: #1BA1E2;*/
  /*color: #1BA1E2;*/
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
