<template>
  <v-toolbar color="#1BA1E2" flat :height="50">
    <v-toolbar-side-icon @click="$router.push('/')">
      <v-img :src="require('@/assets/logo.png')" contain></v-img>
    </v-toolbar-side-icon>

    <v-toolbar-title class="mr-5 white--text">
      QuickEval
    </v-toolbar-title>

    <!-- class="hidden-sm-and-down" -->
    <v-toolbar-items v-if="user.role > 0">
      <v-btn to="/observer" dark flat class="text-none">
        <span class="mr-2 font-weight-regular">Observer Mode</span>
      </v-btn>
    </v-toolbar-items>

    <v-toolbar-items v-if="user.role > 1">
      <v-btn to="/scientist" dark flat class="text-none">
        <span class="mr-2 font-weight-regular">Scientist Mode</span>
      </v-btn>
    </v-toolbar-items>

    <v-toolbar-items v-if="user.role > 2">
      <v-btn to="/admin" dark flat class="text-none">
        <span class="mr-2 font-weight-regular">Admin Mode</span>
      </v-btn>
    </v-toolbar-items>

    <v-spacer></v-spacer>

    <v-toolbar-items v-if="user.id !== 0">
      <Logout/>
    </v-toolbar-items>

    <v-toolbar-items>
      <v-btn
        class="text-none"
        dark
        flat
        href="https://github.com/khaivngo/QuickEval/issues"
        target="_blank"
      >
        <span class="mr-2 font-weight-regular">Report issues</span>
        <v-icon small>open_in_new</v-icon>
      </v-btn>
    </v-toolbar-items>

    <v-toolbar-items>
      <!-- <v-menu bottom left>
        <template v-slot:activator="{ on }">
          <v-btn
            dark
            icon
            v-on="on"
          >
            <v-icon>more_vert</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-tile @click="">
            <v-list-tile-title>account settings</v-list-tile-title>
          </v-list-tile>
        </v-list>
      </v-menu> -->
    </v-toolbar-items>
  </v-toolbar>
</template>

<script>
import Logout from '@/components/Logout'

export default {
  props: {
    user: {
      type: Object,
      default: function () {
        return {
          id: 0,
          role: 0
        }
      }
    }
  },

  components: {
    Logout
  }
}
</script>

<style scoped lang="scss">
  .router-link-active {
    color: #000;
    background: darken(#2196F3, 20%);
  }
  .main__nav a {
    padding: 10px;
    text-decoration: none;
    font-size: 1.3em;

    &.router-link-exact-active {
      color: #000;
      background: darken(#2196F3, 20%);
    }

    &.router-link-active {
      color: red;
    }
  }
</style>
