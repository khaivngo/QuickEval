<template>
  <v-container fluid class="mt-5">
    <v-container class="pl-8">
      <v-row class="mb-12" align="center">
        <v-img
          :src="require('@/assets/logo.png')"
          contain
          max-width="30"
          class="mt-1"
        ></v-img>

        <h1 class="text-h5 mb-3 mt-5 ml-3 pa-0">
          QuickEval
        </h1>
      </v-row>

      <p class="text-h4 font-weight-bold mt-12 pt-12">
        Psychometric image evaluation the quick way.
      </p>

      <!--
        <p>Brought to you by The Norwegian Colour and Visual Computing Laboratory
        <span style="font-size: 1em;">🎨 💻</span></p>
      -->
      <p class="body-1">
        Paired comparison  / Category judgement / Artifact marking /<br>
        Triplet comparison / Rank order / Magnitude Estimation
      </p>

      <v-row class="mt-12">
        <v-col>
          <LoginAnonymous/>
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          OR...
        </v-col>
      </v-row>

      <v-row class="mb-12">
        <v-col>
          <Login/>
          <Register/>
        </v-col>
      </v-row>
    </v-container>

    <v-footer style="background: #F6F6F6; margin-top: 200px;">
      <v-container class="pb-12 pl-8">
        <v-row>
          <!-- <p class="mt-5 mb-0 mb-12">
            The tool is provided by The Norwegian Colour and Visual Computing Laboratory.
          </p> -->
        </v-row>

        <v-row justify="center">
          <v-col cols="auto" class="pr-12">
            <v-img
              :src="require('@/assets/colourlab-logo.png')"
              contain
              class="mt-4"
              max-width="261"
            ></v-img>
          </v-col>
          <v-col>
            <v-img
              :src="require('@/assets/ntnu-logo-slogan.png')"
              contain
              class="mt-5"
              max-width="171"
            ></v-img>
          </v-col>
        </v-row>

        <v-row>
          <v-col>
            <!-- <v-btn to="/privacy" text class="mr-2 text-none">
              Privacy Policy
            </v-btn> -->
            <router-link to="/privacy" class="mr-6">
              Privacy Policy
            </router-link>
            <router-link to="/cookies">
              Cookies Policy
            </router-link>
          </v-col>
        </v-row>
      </v-container>
    </v-footer>
  </v-container>
</template>

<script>
import LoginAnonymous from '@/components/LoginAnonymous'
import Register from '@/components/Register'
import Login from '@/components/Login'

export default {
  components: {
    LoginAnonymous,
    Register,
    Login
  },

  data () {
    return {
      error: ''
    }
  },

  created () {
    this.$axios.get(`/user`).then(response => {
      if (response.data) {
        if (response.data.role && response.data.role > 2) {
          this.$router.push('/scientist')
        }
      }
    }).catch(() => {
      this.showAuth = true
    })
  }
}
</script>
