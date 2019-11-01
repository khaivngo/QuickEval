<template>
  <v-card class="qe-card pa-5">
    <v-card-title primary-title>
      <div>
        <h3 class="headline mb-0">Login</h3>
        <!-- <div></div> -->
      </div>
    </v-card-title>

    <v-text-field
      class="mt-3"
      v-model.trim="email"
      label="Email"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="password"
      label="Password"
      type="password"
    ></v-text-field>

    <v-card-actions>
      <v-btn
        @click="login"
        :disabled="email === null || password === null"
        :loading="logging"
        depressed
        color="#78AA1C"
        large
        class="white--text mt-5"
      >
        Log in
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  name: 'login',

  data () {
    return {
      email: null,
      password: null,
      has_error: false,

      logging: false
    }
  },

  methods: {
    login () {
      this.logging = true

      this.$axios.post('/login', {
        username: this.email,
        password: this.password
      }).then(response => {
        localStorage.setItem('access_token', response.data.access_token)
        this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
        EventBus.$emit('logged')
        this.logging = false
      }).catch(() => {
        // push notification
        this.logging = false
      })
    }
  }
}
</script>

<style scoped lang="css">
  .qe-card {
    max-width: 550px;
  }
</style>
