<template>
  <v-btn
    @click="loginAsAnonymous()"
    large
    rounded
    color="primary"
    :loading="loading"
  >
    Participate anonymously <v-icon right>mdi-arrow-right</v-icon>
  </v-btn>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      loading: false
    }
  },

  methods: {
    loginAsAnonymous () {
      this.loading = true

      this.$axios.post('/anonymous').then(response => {
        localStorage.setItem('access_token', response.data.access_token)
        this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
        // // if (response.data.role === 2) redirect /scientist
        EventBus.$emit('logged', response.data)
        this.loading = false
        // this.$router.push('/observer')
        // this.$router.go() // refresh current page
        window.location.reload(true)
      }).catch((error) => {
        this.loading = false
        this.error = error
      })
    }
  }
}
</script>
