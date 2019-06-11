<template>
  <v-btn @click.prevent="logout" dark flat class="text-none" :loading="loggingOut">
    <span class="mr-2">Logout</span>
  </v-btn>
</template>

<script>
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
