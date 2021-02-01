<template>
  <v-dialog persistent v-model="show" max-width="500">
    <v-card class="pa-2">
      <v-card-title>
        <h2 class="text-h4 font-weight-bold mr-1" style="letter-spacing: 10px;">
          You're done!
        </h2>
        <v-icon x-large color="success">
          mdi-check
        </v-icon>
      </v-card-title>

      <v-card-text>
        <p class="subtitle-1">Thank you for completing the experiment.</p>

        <div class="mt-12">
          <!-- <v-textarea
            outlined
            name="input-feedback"
            label="Before leaving, anything on your mind?"
            v-model="feedback"
            height="100"
            background-color="#fff"
          ></v-textarea> -->
        </div>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="success"
          @click="exit"
        >
          <!-- Close --> Finish
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
  data () {
    return {
      feedback: null
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
