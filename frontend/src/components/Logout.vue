<template>
  <div @click.prevent="logout" class="d-flex">
  <!-- <v-btn @click.prevent="logout" flat class="text-none" :loading="loggingOut"> -->
    <span class="mr-2 font-weight-regular">Sign out</span>
    <!-- <v-icon small>power_settings_new</v-icon> -->
    <v-icon right>logout</v-icon>
  <!-- </v-btn> -->
  </div>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      loggingOut: false
    }
  },

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
