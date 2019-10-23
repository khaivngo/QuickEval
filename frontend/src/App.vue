<template>
  <v-app>
    <MainNavigation
      v-if="$route.path.indexOf('/experiment') !== 0"
      :user="user"
    />

    <!-- <v-breadcrumbs :items="[
      {
        text: 'Dashboard',
        disabled: false,
        href: 'breadcrumbs_dashboard'
      },
      {
        text: 'Link 1',
        disabled: false,
        href: 'breadcrumbs_link_1'
      },
      {
        text: 'Link 2',
        disabled: true,
        href: 'breadcrumbs_link_2'
      }
    ]">
      <template v-slot:divider>
        <v-icon>chevron_right</v-icon>
      </template>
    </v-breadcrumbs> -->

    <v-content>
      <router-view/>
    </v-content>

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
        flat
        @click="snackbar = false"
      >
        Close
      </v-btn>
    </v-snackbar>
  </v-app>
</template>

<script>
import MainNavigation from '@/components/Navigation'
import EventBus from '@/eventBus'

export default {
  name: 'App',

  components: {
    MainNavigation
  },

  data () {
    return {
      user: { id: 0, role: 0 },
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
    })
  },

  mounted () {
    EventBus.$on('success', (payload) => {
      this.text = payload

      this.snackbar = true
    })

    EventBus.$on('logged', (payload) => {
      this.$axios.get(`/user`).then(response => {
        this.user = response.data
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
// html {
//   overflow-y: auto;
// }

// *:focus {
  // outline: 2px solid #FFBC42;
  // outline: 2px dashed $dark-yellow;
// }
</style>
