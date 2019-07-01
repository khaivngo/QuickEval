<template>
  <v-app>
    <MainNavigation v-if="$route.path !== '/experiment'"/>

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

  mounted () {
    EventBus.$on('experimentCreated', (payload) => {
      this.text = payload

      this.snackbar = true
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

// *:focus {
  // outline: 2px solid #FFBC42;
  // outline: 2px dashed $dark-yellow;
// }
</style>
