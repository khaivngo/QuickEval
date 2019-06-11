<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on }">
      <v-btn v-on="on" depressed color="primary">
        Export <v-icon :size="20" class="ml-2">arrow_downward</v-icon>
      </v-btn>
    </template>

    <v-card>
      <v-list>
        <v-list-tile @click="1+1">
          <v-list-tile-title>Experiment</v-list-tile-title>
        </v-list-tile>

        <v-list-tile @click="1+1">
          <v-list-tile-title>Results</v-list-tile-title>
        </v-list-tile>
      </v-list>
    </v-card>
  </v-menu>
</template>

<script>
export default {
  data () {
    return {
    }
  }
}
</script>
