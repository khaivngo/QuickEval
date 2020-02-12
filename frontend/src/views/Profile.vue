<template>
  <v-container style="max-width: 1000px;">
    <v-layout mb-5 mt-5>
      <h2 class="display-1">
        Account Settings
      </h2>
    </v-layout>
    <v-card class="pa-5">
      <!-- <h3 class="headline">Account actions</h3> -->

      <h3 class="headline">Change password</h3>

      <v-form v-model="valid">
        <v-layout column justify-center>
          <v-flex xs12 sm6 md3>
            <v-text-field
              class="mt-3"
              v-model.trim="oldPassword"
              :rules="[rules.required, rules.min]"
              :append-icon="showOldPassword ? 'visibility' : 'visibility_off'"
              :type="showOldPassword ? 'text' : 'password'"
              @click:append="showOldPassword = !showOldPassword"
              label="Old Password"
              validate-on-blur
            ></v-text-field>
          </v-flex>

          <v-flex xs12 sm6 md3>
            <v-text-field
              class="mt-3"
              v-model.trim="newPassword"
              :rules="[rules.required, rules.min]"
              :append-icon="showNewPassword ? 'visibility' : 'visibility_off'"
              :type="showNewPassword ? 'text' : 'password'"
              @click:append="showNewPassword = !showNewPassword"
              label="New Password"
              validate-on-blur
            ></v-text-field>
          </v-flex>

          <div v-if="serverErrors !== ''">
            <p class="red--text">{{ serverErrors }}</p>
          </div>

          <v-flex xs12 sm6 md3>
            <v-btn
              :disabled="oldPassword === '' || newPassword === ''"
              @click="updatePassword"
              depressed color="#78AA1C"
              :loading="updating"
              class="text-none white--text mt-5"
            >
              Update
            </v-btn>
          </v-flex>
        </v-layout>
      </v-form>
    </v-card>
  </v-container>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      toggleUpdatePassword: false,
      valid: false,

      oldPassword: '',
      showOldPassword: false, // uncensor password field

      newPassword: '',
      showNewPassword: false, // uncensor password field

      rules: {
        required: value => !!value || 'Required.',
        min: v => v.length >= 8 || 'Min 8 characters'
      },

      updating: false,

      serverErrors: ''
    }
  },
  methods: {
    updatePassword () {
      if (this.valid === true) {
        this.updating = true

        this.$axios.patch('/user', {
          oldPassword: this.oldPassword,
          newPassword: this.newPassword
        }).then(response => {
          localStorage.removeItem('access_token')

          EventBus.$emit('logged', { id: 0, role: 0 })
          EventBus.$emit('success', response.data)

          this.serverErrors = ''
          this.updating = false

          this.$router.push('/')
        }).catch((error) => {
          this.serverErrors = ''
          if (error.response.status === 401) {
            this.serverErrors = error.response.data
          }
          this.updating = false
        })
      }
    }
  }
}
</script>
