<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1"> <!-- @mouseenter="fadeOut" @mouseleave="fadeIn" -->
    <v-progress-linear v-slot:progress indeterminate class="ma-0" v-if="loading"></v-progress-linear>

    <div v-if="!loading">
      <v-row justify="space-between" align="center">
        <v-col cols="auto">
          <v-row align="center">
            <v-col cols="auto">
              <h2 class="text-h3 mb-3">
                {{ experiment.title }}
              </h2>
            </v-col>
            <v-col>
              <v-chip v-if="experiment.version > 1" disabled text-color="#222" small>
                version {{ experiment.version }}
              </v-chip>
            </v-col>
          </v-row>
          <Clipboard :url="`${$DOMAIN}/observer/${experiment.id}`"/>
        </v-col>
        <v-col cols="auto">
          <v-row>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-switch
                  v-on="on"
                  class="pa-0"
                  style="margin-top: 6px;"
                  v-model="experiment.is_public"
                  color="success"
                  @change="visibility(experiment)"
                  :loading="loadingVisibility"
                >
                </v-switch>
              </template>
              <span>Toggle public visibility of the experiment.</span>
            </v-tooltip>
            <div class="caption mr-6" style="margin-top: 8px;">
              public
            </div>

            <v-menu offset-y left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  icon
                  v-bind="attrs"
                  v-on="on"
                  :loading="deleting"
                >
                  <v-icon>mdi-dots-horizontal</v-icon>
                </v-btn>
              </template>

              <v-list>
                <v-list-item @click="$router.push(`/scientist/experiments/edit/${experiment.id}`)">
                  <v-list-item-icon class="mr-4">
                    <v-icon>mdi-pencil</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title class="pr-6">Edit experiment</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>

                <v-list-item @click="destroy(experiment)">
                  <v-list-item-icon class="mr-4">
                    <v-icon>mdi-delete</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title class="pr-6">Delete experiment</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-row>
        </v-col>
      </v-row>

      <!-- <v-row> -->
        <!-- <div>
          completed: {{ selected.completed_results_count }}
        </div>
      </v-row> -->

      <v-tabs v-model="tab" class="mb-6" style="margin-top: 60px;">
        <v-tab>
          <h5 class="text-subtitle-1 font-weight-medium">
            Observers
          </h5>
        </v-tab>
        <v-tab>
          <h2 class="text-subtitle-1 font-weight-medium">
            Statistics
            <!-- Results -->
          </h2>
        </v-tab>
      </v-tabs>

      <v-tabs-items v-model="tab">
        <v-tab-item :key="0">
          <v-data-table
            class="mt-12"
            v-model="selected"
            :headers="headers"
            :items="experimentResults"
            item-key="id"
            show-select
            no-data-text="0 completed"
            :loading="loading"
            loading-text="Loading... Please wait"
            hide-default-footer
            :items-per-page="100"
          >
            <template v-slot:item.completed="{ item }">
              <span>{{ (item.completed === 1) ? 1 : 0 }}</span>
            </template>
          </v-data-table>

          <div class="d-flex mb-12">
            <v-dialog v-model="exportDialog" max-width="600px">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  class="mt-2 ml-4"
                  v-bind="attrs"
                  v-on="on"
                  :disabled="selected.length === 0"
                >
                  <v-icon :size="20" class="mr-2" style="padding-top: 3px;">
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
                        <p class="pl-0 body-1" style="color: #000;">Results</p>
                      </v-col>
                      <v-col cols="12" sm="6" md="6" class="pa-0">
                        <v-checkbox
                          v-model="exportFlags.results"
                          label="Stimuli results"
                          class="mt-0"
                        ></v-checkbox>
                      </v-col>
                      <v-col cols="12" sm="6" md="6" class="pa-0">
                        <v-checkbox
                          v-if="experiment.observer_metas && experiment.observer_metas.length > 0"
                          v-model="exportFlags.inputs"
                          label="Inputs results (demographics)"
                          class="mt-0"
                        ></v-checkbox>
                      </v-col>
                      <v-col cols="12" class="pa-0 mb-0 mt-6">
                        <p class="pl-0 body-1" style="color: #000;">Meta data</p>
                      </v-col>
                      <v-col cols="12" sm="6" md="6" class="pa-0">
                        <v-checkbox
                          v-model="exportFlags.imageSets"
                          label="Image sets"
                          class="mt-0"
                        ></v-checkbox>
                      </v-col>
                      <v-col cols="12" sm="6" md="6" class="pa-0">
                        <v-checkbox
                          v-if="experiment.observer_metas && experiment.observer_metas.length > 0"
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
                            <p class="pl-0">File format</p>
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
                <v-divider></v-divider>
                <v-card-actions>
                  <v-btn
                    color="blue darken-1"
                    text
                    @click="exportDialog = false"
                  >
                    Cancel
                  </v-btn>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="#78AA1C" dark
                    :loading="exporting"
                    @click="exportResults()"
                  >
                    Export
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>

            <v-spacer></v-spacer>

            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-on="on"
                  @click="destroyResults()"
                  :loading="destroying"
                  :disabled="selected.length === 0"
                  class="mt-2 ml-4"
                  color="default"
                >
                  <v-icon :size="18" class="mr-2">
                    mdi-delete
                  </v-icon>
                  Delete
                </v-btn>
              </template>
              <div class="pa-1">
                Delete selected observers,<br>with belonging results.
              </div>
            </v-tooltip>
          </div>
        </v-tab-item>

        <v-tab-item :key="1">
          <Statistics
            v-if="experiment.id && experimentResults.length > 0"
            :experimentType="experiment.type.slug"
          />
        </v-tab-item>
      </v-tabs-items>

      <!-- <h2 class="mb-5 mt-12">Observers</h2> -->
    </div>
  </div>
</template>

<script>
import EventBus from '@/eventBus.js'
import Statistics from '@/components/Statistics'
import { formatDate } from '@/helpers.js'

import Clipboard from '@/components/Clipboard'

export default {
  components: {
    Statistics,
    Clipboard
  },

  data () {
    return {
      experiment: {},

      tab: 0,

      loading: false,
      loadingVisibility: false,
      exporting: false,
      deleting: false,
      destroying: false,
      exportDialog: false,

      exportFlags: {
        results: true,
        expMeta: true,
        inputs: false,
        inputsMeta: false,
        imageSets: true
      },
      fileFormat: 'csv',

      headers: [
        { text: 'Observer ID', value: 'user_id', sortable: false, desc: '' },
        { text: 'Session ID', value: 'id', align: 'left', sortable: false, desc: 'If the same observer has taken the experiment multiple times,<br> each attempt will have its own session ID.' },
        { text: 'Taken At', value: 'created_at', sortable: false, desc: '' },
        { text: 'Completed', value: 'completed', sortable: false, desc: '' },
        { text: 'Color vision (vision/post eval/degree)', value: 'ishihara', sortable: false, desc: '' }
      ],

      selected: [],

      experimentResults: []
    }
  },

  created () {
    this.loading = true

    this.getExperiment()
    this.getExperimentResults()
  },

  methods: {
    formatDate: formatDate,

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
        url: `/${this.experiment.type.slug}-result/export`,
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
    },

    destroyResults () {
      this.destroying = true

      // create new array with only IDs of the selected objects
      let ids = this.selected.map(selected => {
        return selected.id
      })

      if (confirm('Do you want to delete selected results data for this experiment?')) {
        this.$axios({
          url: `/experiment-result`,
          method: 'DELETE',
          data: {
            selected: ids
          }
        }).then(response => {
          EventBus.$emit('success', 'Observer results has been deleted successfully')
          this.destroying = false
        }).catch(error => {
          alert(error)
          EventBus.$emit('error', 'Could not delete observer results. Please try again in a little while.')
          this.destroying = false
        })
      } else {
        this.destroying = false
      }
    },

    getExperiment () {
      this.$axios.get(`/experiment/${this.$route.params.id}`)
        .then(response => {
          this.experiment = response.data

          if (this.experiment.observer_metas && this.experiment.observer_metas.length > 0) {
            this.exportFlags.inputs = true
            this.exportFlags.inputsMeta = true
          }

          this.loading = false
        }).catch(() => {
          this.loading = false
        })
    },

    getExperimentResults () {
      this.$axios.get(`/experiment-result/${this.$route.params.id}`)
        .then(response => {
          this.experimentResults = response.data

          // convert all the created_at dates to a more readable format
          this.experimentResults.forEach(item => {
            item.created_at = this.formatDate(item.created_at)
          })

          this.experimentResults.forEach(item => {
            item.ishihara = (item.vision) ? `${item.vision}, ${item.post_eval}, ${item.degree}` : 'unknown'
            // (${item.perc}%)
          })

          this.loading = false
        })
        .catch(err => {
          console.log(err)
          this.loading = false
        })
    },

    visibility (exp) {
      this.loadingVisibility = true

      this.$axios.patch(`/experiment/${exp.id}/visibility`, {
        is_public: exp.is_public
      }).then(response => {
        if (response.data.is_public) {
          EventBus.$emit('success', 'Experiment is visible to the public')
        } else {
          EventBus.$emit('info', 'Experiment is hidden from the public.')
        }
        this.loadingVisibility = false
      }).catch(() => {
        this.loadingVisibility = false
      })
    },

    destroy (exp) {
      if (confirm('Do you want to delete the experiment? You will no longer be able to retrieve observer data.')) {
        this.$axios.delete(`/experiment/${exp.id}`).then(response => {
          if (response.data) {
            EventBus.$emit('experiment-deleted', response.data)
            EventBus.$emit('success', 'Experiment has been deleted successfully')
            this.$router.push('/scientist/experiments')
          } else {
            EventBus.$emit('error', 'Could not delete experiment')
          }
        })
      }
    },

    /**
     * slowly and discretely fade out the side-menus when mouse enter meny content area
     * (to decrease visual clutter and direct focus)
     */
    fadeOut () {
      var drawer1 = document.querySelector('.qe-drawer-1')
      var drawer2 = document.querySelector('.qe-drawer-2')

      var interval = setInterval(function () {
        if (window.getComputedStyle(drawer1).opacity <= 0.3) {
          clearInterval(interval)
        }
        drawer1.style.opacity = window.getComputedStyle(drawer1).opacity - 0.01
        drawer2.style.opacity = window.getComputedStyle(drawer2).opacity - 0.01
      }, 400)
    },

    fadeIn () {
      document.querySelector('.qe-drawer-1').style.opacity = 1
      document.querySelector('.qe-drawer-2').style.opacity = 1
    }
  }
}
</script>
