<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        color="primary"
        dark
        large
        rounded
        v-bind="attrs"
        v-on="on"
      >
        Log in <v-icon right>mdi-arrow-right</v-icon>
      </v-btn>
    </template>
    <v-card>
      <v-card-title>
        <v-container>
          <v-row>
            <v-col>
              <span class="headline">Login</span>
            </v-col>
            <v-col cols="auto">
              <v-btn icon color="blue darken-1" text @click="dialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row class="mb-0 pb-0">
            <v-col cols="12" class="pb-0">
              <v-text-field
                class="mt-3"
                v-model.trim="email"
                label="Email"
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                v-model.trim="password"
                label="Password"
                outlined
                dense
                :append-icon="showNewPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showNewPassword ? 'text' : 'password'"
                @click:append="showNewPassword = !showNewPassword"
              ></v-text-field>
            </v-col>
          </v-row>

          <p class="body-1" style="color: red;" v-if="error">{{ error }}</p>

          <v-row class="pt-0 mt-0">
            <v-col class="mt-0 pt-0">
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
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>
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
      dialog: false,
      logging: false,
      error: '',
      showNewPassword: false
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
        this.dialog = false
      }).catch((error) => {
        // push notification
        this.error = error.response.data
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
