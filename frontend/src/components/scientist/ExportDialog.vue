<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        color="primary"
        class="mt-2 ml-4"
        v-bind="attrs"
        v-on="on"
        :disabled="selected.length === 0"
      >
        <v-icon :size="20" class="mr-2">
          mdi-download
        </v-icon>
        Export...
      </v-btn>
    </template>
    <v-card>
      <v-card-title>
        <span class="headline">Export observer data</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row class="mt-4">
            <v-col cols="12" class="pa-0 mb-0">
              <p class="pl-0 body-1" style="color: #000;">
                Results
              </p>
            </v-col>

            <v-col cols="12" sm="6" md="6" class="pa-0">
              <v-checkbox
                v-model="exportFlags.results"
                label="Stimuli results"
                class="mt-0"
              ></v-checkbox>
            </v-col>

            <v-col cols="12" sm="6" md="6" class="pa-0" v-if="observerMetas.length">
              <v-checkbox
                v-model="exportFlags.inputs"
                label="Inputs results (demographics)"
                class="mt-0"
              ></v-checkbox>
            </v-col>

            <v-col cols="12" class="pa-0 mb-0 mt-6">
              <p class="pl-0 body-1" style="color: #000;">
                Meta data
              </p>
            </v-col>

            <v-col cols="12" sm="6" md="6" class="pa-0">
              <v-checkbox
                v-model="exportFlags.imageSets"
                label="Image sets"
                class="mt-0"
              ></v-checkbox>
            </v-col>

            <v-col v-if="observerMetas.length" cols="12" sm="6" md="6" class="pa-0">
              <v-checkbox
                v-model="exportFlags.inputsMeta"
                label="Inputs (demographics)"
                class="mt-0"
              ></v-checkbox>
            </v-col>

            <v-col cols="12" sm="6" md="6" class="pa-0">
              <v-checkbox
                v-model="exportFlags.expMeta"
                label="Experiment paramaters"
                class="mt-0"
              ></v-checkbox>
            </v-col>
          </v-row>

          <div class="pa-0 mt-0">
            <v-radio-group v-model="fileFormat" :mandatory="false">
              <v-row class="mt-6">
                <v-col cols="12" class="pa-0 mb-0">
                  <p class="pl-0">
                    File format
                  </p>
                </v-col>

                <v-col cols="12" sm="6" md="4" class="pa-0 mb-0">
                  <v-radio label="CSV" value="csv"></v-radio>
                </v-col>

                <v-col cols="12" sm="6" md="4" class="pa-0 mb-0">
                  <v-radio label="XLSX" value="xlsx"></v-radio>
                </v-col>

                <v-col cols="12" sm="6" md="4" class="pa-0 mb-0">
                  <v-radio label="HTML" value="html"></v-radio>
                </v-col>
              </v-row>
            </v-radio-group>
          </div>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="blue darken-1"
          text
          @click="dialog = false"
        >
          Close
        </v-btn>

        <v-btn
          color="#78AA1C"
          :loading="exporting"
          dark
          @click="exportResults()"
        >
          Export
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data () {
    return {
      dialog: false,

      exporting: false,

      exportFlags: {
        results: true,
        expMeta: true,
        inputs: false,
        inputsMeta: false,
        imageSets: true
      },

      fileFormat: 'csv'
    }
  },

  methods: {
    exportResults () {
      this.exporting = true

      // create new array with only IDs of the selected objects
      let ids = this.selected.map(selected => {
        return selected.id
      })

      let userIds = this.selected.map(selected => {
        return selected.user_id
      })

      this.$axios({
        url: `/${this.experimentTypeSlug}-result/export`,
        method: 'POST',
        responseType: 'blob', // important
        data: {
          selected: ids,
          experimentId: this.experiment.id,
          selectedUsers: userIds,
          flags: this.exportFlags,
          fileFormat: this.fileFormat
        }
      }).then((response) => {
        console.log(response)
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `${this.experiment.title.substring(0, 50)}-results.${this.fileFormat}`)
        document.body.appendChild(link)
        link.click()
        this.exporting = false
      }).catch(() => {
        this.exporting = false
      })
    }
  }
}
</script>
