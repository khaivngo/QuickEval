<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" persistent max-width="600">
      <!-- <template v-slot:activator="{ on }">
        <v-btn color="primary" dark v-on="on">Open Dialog</v-btn>
      </template> -->
      <v-card>
        <!-- <v-card-title class="headline">Choose one</v-card-title> -->
        <v-card-text>
          <v-layout justify-center>
            <v-btn
              @click="loginAsAnonymous()"
              outline large
              color="primary"
              :loading="anonymousIntent"
            >
              Continue as anonymous
            </v-btn>
          </v-layout>

          <v-layout justify-center mt-3 mb-3>
            <span>OR</span>
          </v-layout>

          <v-layout justify-center>
            <v-btn
              @click="toggleIntent('login')"
              outline large
              color="primary"
            >
              Log in
            </v-btn>

            <v-btn
              @click="toggleIntent('register')"
              outline large
              color="primary"
            >
              Register
            </v-btn>
          </v-layout>

          <div>
            <Register v-if="registerIntent"/>
            <Login v-if="loginIntent"/>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
import Register from '@/components/Register'
import Login from '@/components/Login'
import EventBus from '@/eventBus'

export default {
  components: {
    Register,
    Login
  },

  props: {
    open: Boolean
  },

  data () {
    return {
      anonymousIntent: false,
      registerIntent: false,
      loginIntent: false
    }
  },

  methods: {
    toggleIntent (intent) {
      if (intent === 'register') {
        this.registerIntent = true
        this.loginIntent = false
      }

      if (intent === 'login') {
        this.registerIntent = false
        this.loginIntent = true
      }
    },

    loginAsAnonymous () {
      this.anonymousIntent = true

      this.$axios.post('/anonymous').then(response => {
        localStorage.setItem('access_token', response.data.access_token)
        this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
        // // if (response.data.role === 2) redirect /scientist
        EventBus.$emit('logged', response.data)
        this.anonymousIntent = false
      }).catch(() => {
        // push notification
        this.anonymousIntent = false
      })
    }
  }
}
</script>
