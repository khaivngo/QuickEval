<template>
  <v-card
    flat
    tile
  >
    <v-toolbar color="#1BA1E2" flat dark>
      <!-- <v-app-bar-nav-icon></v-app-bar-nav-icon> -->
      
      <div class="mr-3" @click="$router.push('/')">
        <v-img :src="require('@/assets/logo.png')" contain width="30"></v-img>
      </div>

      <v-toolbar-title class="pr-12">QuickEval</v-toolbar-title>

      <v-btn to="/observer" dark text class="text-none">
        <span class="mr-2 font-weight-regular">
          Observer Mode
        </span>
      </v-btn>

      <v-btn to="/scientist" dark text class="text-none">
        <span class="mr-2 font-weight-regular">
          Scientist Mode
        </span>
      </v-btn>

      <v-btn v-if="user.role > 2" to="/admin" dark text class="text-none">
        <span class="mr-2 font-weight-regular">
          Admin Mode
        </span>
      </v-btn>

      <v-spacer></v-spacer>

      <v-btn
        class="text-none"
        dark
        text
        href="https://github.com/khaivngo/QuickEval/issues"
        target="_blank"
      >
        <span class="mr-2 font-weight-regular">Report issues</span>
        <v-icon small>open_in_new</v-icon>
      </v-btn>

      <v-menu bottom left v-if="user.id !== 0">
        <template v-slot:activator="{ on }">
          <v-btn
            dark
            icon
            v-on="on"
          >
            <v-icon>account_circle</v-icon>
          </v-btn>
        </template>

        <v-list
          flat
        >
          <v-list-item-group color="primary">
            <v-list-item
              @click="$router.push('/user/profile')"
            >
              <v-list-item-content>
                <v-list-item-title>
                  <v-icon left>account_circle</v-icon>
                  Account settings
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item
              @click="logout"
            >
              <v-list-item-content>
                <v-list-item-title>
                  <v-icon left>logout</v-icon>
                  Sign out
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </v-toolbar>
  </v-card>
</template>

<script>
// import Logout from '@/components/Logout'
import EventBus from '@/eventBus'
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
  data () {
    return {
      loggingOut: false
    }
  },
  // components: {
  //   Logout
  // },
  methods: {
    logout () {
      this.loggingOut = true

      this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
      this.$axios.post('/logout').then(response => {
        localStorage.removeItem('access_token')
        EventBus.$emit('logged', { id: 0, role: 0 })
        this.$router.push('/')
        this.loggingOut = false
      }).catch(() => {
        localStorage.removeItem('access_token')
        // push notification
        this.loggingOut = false
      })
    }
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
