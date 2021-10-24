<template>
  <v-dialog v-model="dialog" max-width="600px">
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        color="primary"
        class="mr-4"
        large
        rounded
        outlined
        v-bind="attrs"
        v-on="on"
      >
        <!-- Register -->
        Sign up to research
      </v-btn>
    </template>
    <v-card>
      <v-card-title>
        <v-container>
          <v-row>
            <v-col>
              <span class="headline">Register</span>
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
          <v-form v-model="valid">
            <v-text-field
              v-model.trim="email"
              :rules="[rules.required]"
              validate-on-blur
              label="Email"
              outlined
              dense
            ></v-text-field>

            <v-text-field
              v-model.trim="password"
              :rules="[rules.required, rules.min]"
              validate-on-blur
              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPassword ? 'text' : 'password'"
              @click:append="showPassword = !showPassword"
              label="Password"
              outlined
              dense
            ></v-text-field>

            <v-text-field
              v-model.trim="name"
              label="Name"
              outlined
              dense
            ></v-text-field>

            <v-select
              v-model="gender"
              :items="['Female', 'Male', 'Other', 'Rather not say']"
              label="Gender"
              outlined
              dense
            ></v-select>

            <v-text-field
              v-model.trim="yob"
              label="Year of Birth"
              placeholder="yyyy"
              outlined
              dense
            ></v-text-field>

            <v-autocomplete
              v-model.trim="institution"
              :items="institutions"
              :filter="customFilter"
              item-text="name"
              label="Institution"
              outlined
              dense
              autocomplete="new-password"
            ></v-autocomplete>

            <v-autocomplete
              v-model.trim="nationality"
              :items="states"
              :filter="customFilter"
              item-text="name"
              label="Country"
              outlined
              dense
              autocomplete="new-password"
            ></v-autocomplete>

            <v-checkbox
              v-model="scientist"
              color="primary"
              :label="`Register as scientist`"
            ></v-checkbox>

            <p class="caption">
              Check the box above to be eligible for scientist role.
              Your account will be manually approved as soon as possible.
              An email will automatically be sent to you when this is done.
            </p>
          </v-form>

          <div v-if="serverErrors !== ''">
            <p class="red--text" v-for="(error, i) in serverErrors.errors.email" :key="i">
              {{ error }}
            </p>
          </div>

          <v-row class="pt-0 mt-0">
            <v-col class="mt-0 pt-0">
              <v-btn @click="register" depressed color="#78AA1C" :loading="registering" large class="white--text mt-5" :disabled="email === ''">
                Register
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
  data () {
    return {
      valid: false,

      email: '',
      name: '',
      password: '',
      gender: '',
      yob: '',
      institution: '',
      nationality: '',
      scientist: false,

      has_error: false,
      error: '',
      errors: {},
      success: false,
      dialog: false,

      showPassword: false,

      registering: false,

      serverErrors: '',

      rules: {
        required: value => !!value || 'Required.',
        min: v => v.length >= 8 || 'Min 8 characters'
      },

      states: [
        { name: 'Norway' },
        { name: 'Sweden' },
        { name: 'Other' }
      ],

      institutions: [
        { name: 'NTNU' },
        { name: 'Høgskolen i Lillehammer' },
        { name: 'Other' }
      ]
    }
  },

  methods: {
    register () {
      if (this.valid === true) {
        this.registering = true

        this.$axios.post('/register', {
          email: this.email,
          password: this.password,
          name: this.name,
          gender: this.gender,
          yob: this.yob,
          institution: this.institution,
          nationality: this.nationality,
          scientist: this.scientist
        }).then(response => {
          localStorage.setItem('access_token', response.data.access_token)
          this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
          EventBus.$emit('success', 'Your account has been created, you may now log in.')
          // EventBus.$emit('registered')
          this.serverErrors = ''
          this.registering = false
          this.dialog = false
          this.$emit('success')
        }).catch((error) => {
          console.log(error)
          // this.errors = error
          this.serverErrors = ''
          this.serverErrors = error.response.data
          this.registering = false
        }).finally(() => {
          this.registering = false
          // this.$emit('success')
          // EventBus.$emit('success', '')
        })
      }
    },

    // filter out possible matches when user types in nationality field
    customFilter (item, queryText, itemText) {
      const textOne = item.name.toLowerCase()
      const searchText = queryText.toLowerCase()

      return textOne.indexOf(searchText) > -1
    }
  }
}
</script>

<style scoped lang="css">
  .qe-card {
    max-width: 550px;
  }
</style>
