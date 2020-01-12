<template>
  <div>
    <v-layout mb-5 mt-5>
      <h2 class="display-1">
        Dashboard
      </h2>
    </v-layout>
    <v-card>
      <v-container>
        <h3>Account actions</h3>
        <v-btn @click="toggleUpdatePassword = !toggleUpdatePassword" flat class="text-none">
          Change password
          <v-icon v-if="toggleUpdatePassword === false">expand_more</v-icon>
          <v-icon v-if="toggleUpdatePassword === true">expand_less</v-icon>
        </v-btn>
      </v-container>
      <v-container v-if="toggleUpdatePassword">
        <!-- <v-tabs centered>
          <v-tab>
            <v-icon>mdi-face</v-icon>
            Profile
          </v-tab>
        </v-tabs> -->

        <h3 class="title mb-4">Change password</h3>

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

            <!-- <stat label="visitors" class="mr-5">
              22
            </stat>
            <stat label="completed">
              20
            </stat> -->

            <!-- <Algo/> -->

            <!-- <DraggableRankingList/> -->
          </v-layout>
        </v-form>
      </v-container>
    </v-card>
  </div>
</template>

<script>
import EventBus from '@/eventBus'
// import stat from '@/components/Stat'
// import Algo from '@/components/scientist/Algo'
// import DraggableRankingList from '@/components/DraggableRankingList'

export default {
  components: {
    // stat
    // Algo
    // DraggableRankingList
  },
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
          // localStorage.setItem('access_token', response.data.access_token)
          // this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
          // if (success.response.status === 200) {
          EventBus.$emit('success', response.data)
          // }
          this.serverErrors = ''
          this.updating = false
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
