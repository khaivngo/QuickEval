<template>
  <v-container>
    <v-layout>
      <v-flex>
        <v-layout
          text-xs-center
          wrap
          justify-center
        >
          <v-flex xs12>
            <v-img
              :src="require('../assets/logo.png')"
              class="my-3"
              contain
              height="200"
            ></v-img>
          </v-flex>

          <v-flex mb-4>
            <h1 class="display-2 font-weight-bold mb-3">
              QuickEval
            </h1>
            <p class="subheading font-weight-regular">
              A web-based tool for <strong>psychometric image evaluation.</strong>
            </p>

            <h3 class="subheading">supports:</h3>
            <div>
              <h3>rank order</h3>
              <h3>paired comparison</h3>
              <h3>triplet comparison</h3>
              <h3>category judgement</h3>
              <h3>artifact marking</h3>
            </div>

            <p class="mt-5 mb-0">
              The tool is provided by the<br>
              Norwegian Colour and Visual Computing Laboratory.
            </p>
          </v-flex>
        </v-layout>

        <v-layout justify-center mb-3>
          <!-- <v-flex justify-center align-self-center xs12 mb-5 style="background-color: red;"> -->
          <v-img
            :src="require('@/assets/colourlab-logo.png')"
            contain
            max-width="200"
          ></v-img>
        </v-layout>

        <v-layout justify-center>
          <v-img
            :src="require('@/assets/ntnu-logo-slogan.png')"
            contain
            max-width="200"
          ></v-img>
        </v-layout>
      </v-flex>

      <v-flex align-self-center>
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
            <Register v-if="registerIntent"/>
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
    }
  }
}
</script>
