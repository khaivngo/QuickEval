<template>
  <v-card class="qe-card pa-5">
    <v-card-title primary-title>
      <div>
        <h3 class="headline mb-0">Register</h3>
        <!-- <div></div> -->
      </div>
    </v-card-title>

    <v-text-field
      class="mt-3"
      v-model.trim="email"
      label="Email"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="password"
      label="Password"
      type="password"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="name"
      label="Name"
    ></v-text-field>

    <v-select
      class="mt-3"
      v-model="gender"
      :items="['Female', 'Male', 'Other', 'Rather not say']"
      label="Gender"
    ></v-select>

    <v-text-field
      class="mt-3"
      v-model.trim="yob"
      label="Year of Birth"
      placeholder="yyyy"
    ></v-text-field>

    <v-autocomplete
      v-model.trim="institution"
      :items="institutions"
      :filter="customFilter"
      item-text="name"
      label="Institution"
    ></v-autocomplete>

    <v-autocomplete
      v-model.trim="nationality"
      :items="states"
      :filter="customFilter"
      item-text="name"
      label="Nationality"
    ></v-autocomplete>

    <v-checkbox
      v-model="scientist"
      color="success"
      :label="`Register as scientist`"
    ></v-checkbox>

    <p class="caption">
      Check the box above to be eligible for scientist role.
      Your account will be manually approved as soon as possible.
      An email will automatically be sent to you when this is done.
    </p>

    <v-card-actions>
      <v-btn @click="register" depressed color="#78AA1C" :loading="registering" large class="white--text mt-5" :disabled="email === ''">
        Register
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  data () {
    return {
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

      registering: false,

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
        // if (response.data.type === 2) redirect /scientist
        // this.$router.push('/observer')
        this.registering = false
      }).catch(() => {
        // push notification
        this.registering = false
      })
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
