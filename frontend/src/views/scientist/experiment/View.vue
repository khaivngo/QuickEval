<template>
  <v-container>
    <v-progress-linear v-slot:progress indeterminate class="ma-0" v-if="loading"></v-progress-linear>
    <!-- <p v-if="loading" class="text-center">It's here somewhere...</p> -->

    <div v-if="!loading">
      <v-row justify="space-between" align="center">
        <v-col cols="auto">
          <h2 class="text-h3 mb-3">
            {{ experiment.title }}
          </h2>
          <v-chip v-if="experiment.version > 1" disabled text-color="#222" small class="ml-2">
            version {{ experiment.version }}
          </v-chip>
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
              <span>Toggle public visibility of experiment.</span>
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

      <h2 class="mb-5 mt-12">Observers</h2>
      <!-- <p>wwdw awd awdwadadad dddd</p> -->

      <v-data-table
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
      ></v-data-table>

      <v-row class="mt-3" align="center">
        <div>
          <v-btn
            v-if="experimentResults.length"
            :disabled="selected.length === 0"
            @click="exportResults()"
            :loading="exporting"
            color="primary"
            type="submit"
            class="mt-6 ml-7"
          >
            Export
            <v-icon :size="20" class="ml-2">
              mdi-download
            </v-icon>
          </v-btn>

          <v-btn
            v-if="observerMetas.length"
            @click="exportObserverMetasForExperiment()"
            color="primary text-none ma-0 ml-2"
          >
            Export demographics
            <v-icon :size="20" class="ml-2">
              mdi-download
            </v-icon>
          </v-btn>
        </div>
      </v-row>

      <Statistics v-if="experimentResults.length > 0"/>
    </div>
  </v-container >
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
      loading: false,
      loadingVisibility: false,
      exporting: false,
      deleting: false,

      headers: [
        { text: 'Observer ID', value: 'user_id', sortable: false, desc: '' },
        { text: 'Session ID', value: 'id', align: 'left', sortable: false, desc: 'If the same observer has taken the experiment multiple times,<br> each attempt will have its own session ID.' },
        { text: 'Taken At', value: 'created_at', sortable: false, desc: '' }
      ],

      selected: [],

      experiment: {
        id: null,
        title: null,
        experiment_type_id: null
      },

      experimentTypeSlug: '',

      experimentResults: [],

      observerMetas: []
    }
  },

  created () {
    this.loading = true

    this.getExperiment()
    this.getExperimentResults()

    this.$axios.get(`/experiment-observer-meta-result/find-or-fail/${this.$route.params.id}`)
      .then(response => {
        if (response.data !== '') {
          this.observerMetas.push(response.data)
        }
      })
      .catch(err => console.log(err))
  },

  methods: {
    formatDate: formatDate,

    // export results in CSV format
    exportResults () {
      this.exporting = true

      // create new array with only IDs of the selected objects
      let ids = this.selected.map(selected => {
        return selected.id
      })

      this.$axios({
        url: `/${this.experimentTypeSlug}-result/export`,
        method: 'POST',
        responseType: 'blob', // important
        data: {
          selected: ids
        }
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'results.csv')
        document.body.appendChild(link)
        link.click()
        this.exporting = false
      }).catch(() => {
        this.exporting = false
      })
    },

    getExperiment () {
      this.$axios.get(`/experiment/${this.$route.params.id}`)
        .then(response => {
          this.experiment = response.data

          if (this.experiment.experiment_type_id === 1) this.experimentTypeSlug = 'paired'
          if (this.experiment.experiment_type_id === 2) this.experimentTypeSlug = 'rank-order'
          if (this.experiment.experiment_type_id === 3) this.experimentTypeSlug = 'category'
          if (this.experiment.experiment_type_id === 5) this.experimentTypeSlug = 'triplet'

          this.loading = false
        })
        .catch(() => {
          this.loading = false
        })
    },

    getExperimentResults () {
      this.$axios.get(`/experiment-result/${this.$route.params.id}`)
        .then(response => {
          this.experimentResults = response.data

          // convert all the created_at dates to a more readable format
          response.data.forEach(item => {
            item.created_at = this.formatDate(item.created_at)
          })

          this.loading = false
        })
        .catch(err => console.log(err))
    },

    exportObserverMetasForExperiment () {
      window.open(`${this.$API_URL}/experiment-observer-meta-result/${this.$route.params.id}/export`, '_blank')
    },

    exportObserverMetasForObserver (user) {
      window.open(`${this.$API_URL}/experiment-observer-meta-result/${this.$route.params.id}/${user.user_id}/export`, '_blank')
    },

    visibility (exp) {
      this.loadingVisibility = true
      this.$axios.patch('/experiment/' + exp.id + '/visibility', {
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

    wipeAllResults () {
      if (confirm('Do you want to delete ALL results data for this experiment?')) {
        this.$axios.delete(`/experiment-result/${this.$route.params.id}/wipe`).then(response => {
          if (response.data === 'deleted') {
            this.experimentResults = []

            EventBus.$emit('success', 'Experiment data has been deleted successfully')
          } else {
            EventBus.$emit('error', 'Could not delete experiment data.')
          }
        })
      }
    }
  }
}
</script>
