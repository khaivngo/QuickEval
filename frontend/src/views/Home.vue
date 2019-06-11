<template>
  <v-container>
    <v-layout>
      <v-flex>
        <v-layout
          text-xs-center
          wrap
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

            <p class="mt-5">
              The tool is provided by the<br>
              Norwegian Colour and Visual Computing Laboratory.
            </p>
          </v-flex>
        </v-layout>

        <v-layout justify-center>
          <v-flex xs12>
            <v-img :src="require('@/assets/colourlab-logo.png')" contain max-width="200"></v-img>
          </v-flex>
          <v-flex xs12>
            <v-img :src="require('@/assets/ntnu-logo-slogan.png')" contain max-width="200"></v-img>
          </v-flex>
        </v-layout>
      </v-flex>

      <v-flex>
        <v-btn @click="loginAsAnonymous" depressed large color="primary" class="text-none">
          Continue as anonymous
        </v-btn>
        <span>OR</span>
        <v-btn @click="loginAsAnonymous" depressed large color="primary" class="text-none">
          Log in
        </v-btn>

        <div>
          <v-btn to="/register" depressed large color="primary">
            Register
          </v-btn>

          <Register/>

          <Login/>
        </div>

      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import Register from '@/components/Register'
import Login from '@/components/Login'

export default {
  components: {
    Register,
    Login
  },

  methods: {
    loginAsAnonymous () {
      // ajax create anonymous
      // redirect

      // this.$http({
      //   url: `users`,
      //   method: 'GET'
      // }).then((res) => {
      //   this.users = res.data.users
      // }, () => {
      //   this.has_error = true
      // })
    }
  }
}
</script>
