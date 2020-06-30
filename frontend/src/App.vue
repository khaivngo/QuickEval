<template>
  <v-app>
    <MainNavigation
      v-if="$route.path.indexOf('/experiment') !== 0"
      :user="user"
    />

    <v-main class="fill-height ma-0">
      <router-view class="pa-0 ma-0"/>
    </v-main>

    <!-- Show a login modal if not logged in. Unless we're on the frontpage (already has login form). -->
    <LoginModal :open="showAuth" v-if="$route.path != '/'"/>

    <v-snackbar
      v-model="snackbar"
      :bottom="y === 'bottom'"
      :left="x === 'left'"
      :multi-line="mode === 'multi-line'"
      :right="x === 'right'"
      :timeout="timeout"
      :top="y === 'top'"
      :color="color"
      :vertical="mode === 'vertical'"
    >
      {{ text }}
      <v-btn
        text
        @click="snackbar = false"
      >
        Close
      </v-btn>
    </v-snackbar>
  </v-app>
</template>

<script>
import LoginModal from '@/components/LoginModal'
import MainNavigation from '@/components/Navigation'
import EventBus from '@/eventBus'

export default {
  name: 'App',

  components: {
    MainNavigation,
    LoginModal
  },

  data () {
    return {
      user: { id: 0, role: 0 },
      showAuth: false,
      token: localStorage.getItem('access_token') || null,

      snackbar: false,
      y: 'top',
      x: 'right',
      mode: '',
      timeout: 6000,
      text: 'Successfully created.',
      color: 'success'
    }
  },

  created () {
    this.$axios.get(`/user`).then(response => {
      this.user = response.data
    }).catch(() => {
      this.showAuth = true
    })
  },

  mounted () {
    EventBus.$on('success', (payload) => {
      this.text = payload
      this.color = 'success'

      this.snackbar = true
    })

    EventBus.$on('info', (payload) => {
      this.text = payload
      this.color = 'info'

      this.snackbar = true
    })

    // EventBus.$on('registered', (payload) => {
    //   this.showAuth = false
    // })
    EventBus.$on('logged', (payload) => {
      this.$axios.get(`/user`).then(response => {
        this.user = response.data
        this.showAuth = false

        if (this.$route.name === 'referral') {
          this.$router.go()
        } else {
          // this.$router.go()
          this.$router.push('/observer')
        }
      }).catch(() => {
        this.user = { id: 0, role: 0 }
      })
    })
  }
}
</script>

<style lang="scss">
// $light-beige: #553D36;
// $dark-yellow: #FCCA46;
// 97CC04

// Vuetify by default turns on the html scrollbar. Disable this.
// https://vuetifyjs.com/en/getting-started/frequently-asked-questions#the-scrollbar-is-showing-even-though-my-content-is-not-overflowing-vertically-
html {
  overflow-y: auto;
}

.hide {
  opacity: 0;
}

// *:focus {
  // outline: 2px solid #FFBC42;
  // outline: 2px dashed $dark-yellow;
// }
</style>
