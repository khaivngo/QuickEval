<template>
  <v-dialog persistent v-model="show" max-width="500">
    <template v-slot:activator="{ on }">
      <v-btn text dark color="#D9D9D9" v-on="on">
        Instructions
      </v-btn>
    </template>
    <v-card style="background-color: grey; color: #fff;">
      <v-card-title class="headline">
        Instructions
      </v-card-title>

      <v-card-text v-html="text" style="color: #fff;"></v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="primary darken-1"
          text
          @click="show = false"
        >
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: ['show', 'text']
}
</script>
