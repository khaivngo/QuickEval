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
      v-model.trim="form.email"
      label="Email"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="form.password"
      label="Password"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="form.confirmPassword"
      label="Confirm password"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="form.firstName"
      label="First Name"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="form.lastName"
      label="Last Name"
    ></v-text-field>

    <v-select
      class="mt-3"
      v-model="form.gender"
      :items="['Female', 'Male', 'Other', 'Rather not say']"
      label="Gender"
    ></v-select>

    <v-text-field
      class="mt-3"
      v-model.trim="form.yob"
      label="Year of Birth"
      placeholder="yyyy"
    ></v-text-field>

    <v-text-field
      class="mt-3"
      v-model.trim="form.institution"
      label="Institution"
    ></v-text-field>

    <v-autocomplete
      v-model.trim="form.nationality"
      :items="states"
      :filter="customFilter"
      item-text="name"
      label="Nationality"
    ></v-autocomplete>

    <v-checkbox
      v-model="form.scientist"
      color="success"
      :label="`Register as scientist (manually approved)`"
    ></v-checkbox>

    <p class="caption">Check this box to </p>
    <!-- ("as of now ") Scientists needs, as of now, to register as an observer and email admin to be upgraded to scientist. -->

    <!-- email to marius, if no checkbox -->

    <v-card-actions>
      <v-btn
        @click="register"
        :loading="registering"
        :disabled="true"
        depressed
        color="#78AA1C"
        dark
        large
        class="mt-5"
      >
        Register w
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  data () {
    return {
      form: {
        email: null,
        password: '',
        confirmPassword: '',
        firstName: '',
        lastName: '',
        gender: '',
        yob: '',
        institution: '',
        nationality: '',
        scientist: false
      },

      registering: false,

      states: [{ name: 'Norway' }, { name: 'Sweden' }]
    }
  },

  methods: {
    register () {
      //
    },

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
