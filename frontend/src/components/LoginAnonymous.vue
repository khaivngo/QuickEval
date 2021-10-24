<template>
  <div>
    <v-btn
      @click="loginAsAnonymous()"
      large
      rounded
      color="primary"
      :loading="loading"
    >
      Participate anonymously <v-icon right>mdi-arrow-right</v-icon>
    </v-btn>

    <v-tooltip top class="mt-1">
      <template v-slot:activator="{ on }">
        <v-btn icon v-on="on">
          <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
        </v-btn>
      </template>
      <div class="pl-2 pr-2 pt-3 pb-3 body-1">
        A non-identifiable ID will be created to keep track of you experiment progress.<br>
        <!-- Return to this page later by clicking the icon in the rop right corner and then "sign out". -->
      </div>
    </v-tooltip>
  </div>
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
        if (response.data) {
          this.$axios.post('/login', {
            username: response.data.email,
            password: response.data.auth_id
          }).then(response => {
            localStorage.setItem('access_token', response.data.access_token)
            this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
            // // if (response.data.role === 2) redirect /scientist
            EventBus.$emit('logged', response.data)
            this.loading = false
            this.$router.push('/observer')
            // this.$router.go() // refresh current page
          }).catch((error) => {
            this.loading = false
            this.error = error
          })
        } else {
          this.loading = false
        }
      }).catch((error) => {
        this.loading = false
        this.error = error
      })
    }
  }
}
</script>
