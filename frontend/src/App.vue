<template>
  <v-app>
    <MainNavigation
      v-if="$route.path.indexOf('/experiment') !== 0 && $route.name !== 'home'"
      :user="user"
    />

    <v-main class="fill-height ma-0">
      <router-view class="pa-0 ma-0"/>
    </v-main>

    <!--
      Show a login modal if not logged in. Unless we're on the frontpage (already has login form)
      or reading the privacy policy.
    -->
    <LoginModal
      v-if="$route.path != '/' && $route.path != '/privacy' && $route.path != '/cookies'"
      :open="showAuth"
    />

    <CookiesConsent/>

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
import CookiesConsent from '@/components/CookiesConsent'
import EventBus from '@/eventBus'

export default {
  name: 'App',

  components: {
    MainNavigation,
    LoginModal,
    CookiesConsent
  },

  data () {
    return {
      // storeState: store.state,
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
      // store.setUser(response.data)
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
$scrollbarBG: #CFD8DC;
$thumbBG: #90A4AE;

// Vuetify by default turns on the html scrollbar. Disable this.
// https://vuetifyjs.com/en/getting-started/frequently-asked-questions#the-scrollbar-is-showing-even-though-my-content-is-not-overflowing-vertically-
html {
  overflow-y: auto;
}

.hide {
  opacity: 0;
}

::-webkit-scrollbar {
  width: 12px;
}
body {
  scrollbar-width: thin;
  scrollbar-color: $thumbBG $scrollbarBG;
}
::-webkit-scrollbar-track {
  background: $scrollbarBG;
}
::-webkit-scrollbar-thumb {
  background-color: $thumbBG;
  border-radius: 6px;
  border: 3px solid $scrollbarBG;
}

// *:focus {
  // outline: 2px solid #FFBC42;
  // outline: 2px dashed $dark-yellow;
// }
.mb-100 {
  margin-bottom: 100px;
}
</style>
