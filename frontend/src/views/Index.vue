<template>
  <v-container fluid class="mt-5">
    <v-container>
      <v-row class="mb-12" align="center">
        <v-img
          :src="require('@/assets/logo.png')"
          contain
          max-width="30"
        ></v-img>

        <h1 class="text-h5 mb-3 mt-5 ml-3 pa-0">
          QuickEval
        </h1>
      </v-row>

      <p class="text-h4 font-weight-bold mt-12 pt-12">
        Psychometric image evaluation the quick way.
      </p>

      <!-- <p>Brought to you by The Norwegian Colour and Visual Computing Laboratory <span style="font-size: 1em;">🎨 💻</span></p> -->
      <!-- <p>rank order, paired comparison, triplet comparison, category judgement, artifact marking</p> -->
      <!-- <h3 class="font-weight-bold subtitle-1 mt-12">Supports</h3> -->
      <ul class="mb-5">
        <li style="margin-bottom: 0.2em;">Rank order</li>
        <li style="margin-bottom: 0.2em;">Paired comparison</li>
        <li style="margin-bottom: 0.2em;">Triplet comparison</li>
        <li style="margin-bottom: 0.2em;">Category judgement</li>
        <li style="margin-bottom: 0.2em;">Artifact marking</li>
      </ul>

      <v-row class="mt-12">
        <v-col>
          <v-btn
            @click="loginAsAnonymous()"
            large rounded
            color="primary"
            :loading="anonymousIntent"
          >
            Participate anonymously <v-icon right>mdi-arrow-right</v-icon>
          </v-btn>
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          OR...
        </v-col>
      </v-row>

      <v-row class="mb-12">
        <v-col>
          <!-- <v-btn
            @click="toggleIntent('login')"
            large
            rounded
            color="primary"
            class="mr-4"
          >
            Log in <v-icon right>mdi-arrow-right</v-icon>
          </v-btn> -->
          <Login/>

          <Register/>

          <!-- <v-btn
            @click="toggleIntent('register')"
            text large
            color="primary"
          >
            Register
          </v-btn> -->
        </v-col>
      </v-row>

      <!-- <div style="width: 400px;" class="mt-4">
        <Register v-if="registerIntent" @success="registered"/>
        <Login v-if="loginIntent"/>
      </div> -->
    </v-container>

    <v-footer style="background: white; margin-top: 200px;">
      <v-container class="pb-12">
        <v-row justify="center">
          <!-- <p class="mt-5 mb-0 mb-12">
            The tool is provided by The Norwegian Colour and Visual Computing Laboratory.
          </p> -->
        </v-row>

        <v-row justify="center">
          <v-img
            :src="require('@/assets/colourlab-logo.png')"
            contain
            class="mt-4 mr-12"
            max-width="300"
          ></v-img>

          <v-img
            :src="require('@/assets/ntnu-logo-slogan.png')"
            contain
            class="mt-4"
            max-width="170"
          ></v-img>
        </v-row>
      </v-container>
    </v-footer>
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
      loginIntent: false,

      error: ''
      // storeState: store.state
      // user: {}
    }
  },

  // computed: {
  //   storeState () {
  //     return store.state
  //   }
  // },
  // computed: {
  //   user () {
  //     return store.state.user
  //   }
  // },
  // watch: {
  //   user (user) {
  //     // this.user = user
  //     console.log(user)
  //   }
  // },

  // watch: {
  //   storeState () {
  //     console.log('wwwww')
  //   }
  // },

  created () {
    // check if login -> redirect
    // console.log(this.storeState)
    // if (this.user.id > 0) {
    //   console.log('wwww')
    // }
    this.$axios.get(`/user`).then(response => {
      if (response.data) {
        if (response.data.role < 2) {
          this.$router.push('/observer')
        }
        if (response.data.role === 2) {
          this.$router.push('/scientist')
        }
      }
    }).catch(() => {
      this.showAuth = true
    })
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
      }).catch((error) => {
        this.anonymousIntent = false
        this.error = error
      })
    },

    registered () {
      this.registerIntent = false
      this.loginIntent = true
    }
  }
}
</script>
