<template>
  <v-container class="mt-5">
    <v-layout wrap justify-center>
      <v-flex shrink class="pr-5">
        <v-img
          :src="require('@/assets/logo.png')"
          contain
          max-width="200"
        ></v-img>

        <h1 class="display-2 font-weight-bold mb-3 mt-5">
          QuickEval
        </h1>

        <p class="headline font-weight-regular">
          A web-based tool for psychometric image evaluation.
        </p>

        <h3>supports:</h3>
        <ul class="mb-5">
          <li>rank order</li>
          <li>paired comparison</li>
          <li>triplet comparison</li>
          <li>category judgement</li>
          <li>artifact marking</li>
        </ul>

        <p class="mt-5 mb-0">
          The tool is provided by the<br>
          Norwegian Colour and Visual Computing Laboratory.
        </p>

        <v-img
          :src="require('@/assets/colourlab-logo.png')"
          contain
          class="mt-4"
          max-width="200"
        ></v-img>

        <v-img
          :src="require('@/assets/ntnu-logo-slogan.png')"
          contain
          class="mt-4"
          max-width="200"
        ></v-img>
      </v-flex>

      <v-flex class="mt-5" shrink>
        <v-layout column justify-center>
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
            <Register v-if="registerIntent" @success="registered"/>
            <Login v-if="loginIntent"/>
          </div>
        </v-layout>
      </v-flex>
    </v-layout>
  </v-container>
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
        this.$router.push('/observer')
      }).catch(() => {
        // push notification
        this.anonymousIntent = false
      })
    },

    registered () {
      this.registerIntent = false
      this.loginIntent = true
    }
  }
}
</script>
