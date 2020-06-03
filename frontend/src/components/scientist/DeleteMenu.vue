<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on }">
      <v-btn v-on="on" depressed color="error">
        Delete <v-icon :size="20" class="ml-2">delete</v-icon>
      </v-btn>
    </template>

    <v-card>
      <v-list>
        <v-list-tile @click="1+1">
          <v-dialog v-model="dialog" width="500">
            <template v-slot:activator="{ on }">
              <v-list-tile-title v-on="on">
                Delete Experiment Results
              </v-list-tile-title>
            </template>

            <v-card>
              <v-card-title class="headline" primary-title>
                Delete experiment results?
              </v-card-title>

              <v-card-text>
              </v-card-text>

              <v-divider></v-divider>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text @click="dialog = false">
                  cancel
                </v-btn>

                <v-btn color="error" depressed @click="dialog = false">
                  Delete
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-list-tile>

        <v-list-tile @click="1+1">
          <v-list-tile-title>Delete Experiment</v-list-tile-title>
        </v-list-tile>
      </v-list>
    </v-card>
  </v-menu>
</template>

<script>
export default {
  data () {
    return {
      dialog: false
    }
  }
}
</script>
