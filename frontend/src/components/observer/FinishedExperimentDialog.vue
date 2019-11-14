<template>
  <v-dialog persistent v-model="show" max-width="500">
    <v-card style="background-color: grey; color: #fff;">
      <v-card-title class="headline">
        You're done!
      </v-card-title>

      <v-card-text>Thank you for completing the experiment.</v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="primary darken-1"
          flat="flat"
          @click="exit"
        >
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    exit () {
      // this.show = false
      // emit event?
      this.$router.push('/observer')
    }
  }
}
</script>
