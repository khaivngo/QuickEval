<template>
  <div>
    <v-layout mb-5>
      <h2 class="display-1">
        Observer Results For {{ experiment.title }}
      </h2>
    </v-layout>

    <v-card>
      <Back>Back to all experiments</Back>

      <v-container pt-5 mt-1>
        <v-layout justify-space-between align-center mb-3>
          <h2 class="title">
            Observer Raw Data
          </h2>

          <div>
            <!-- <v-btn
              outline
              color="error text-none mt-0 mb-0 mr-2"
              @click="wipeAllResults"
            >
              Wipe ALL data
              <v-icon :size="20" class="ml-2">
                delete
              </v-icon>
            </v-btn> -->

            <v-btn
              v-if="experimentResults.length"
              @click="exportResultsForExperiment()"
              color="primary text-none ma-0"
            >
              Export ALL observers
              <v-icon :size="20" class="ml-2">
                arrow_downward
              </v-icon>
            </v-btn>
          </div>
        </v-layout>

        <v-data-table
          :headers="[
            { text: 'Observer ID', value: 'name', align: 'left', sortable: false },
            { text: 'Taken At', value: 'takenAt', sortable: false },
            { text: 'Export raw data', value: 'export', align: 'right', sortable: false }
          ]"
          :items="experimentResults"
          no-data-text=""
          :expand="false"
          item-key="id"
          hide-actions
          :loading="loading"
        >
          <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
          <template v-slot:no-data>
            <div class="caption text-xs-center" v-if="loading === false">
              No observer data to show. No one has completed the experiment yet.
            </div>
          </template>
          <template v-slot:items="props">
            <tr @click="props.expanded = !props.expanded">
              <td>{{ props.item.id }}</td>
              <td style="width: 100%;">{{ formatDate(props.item.created_at) }}</td>
              <td class="text-xs-right">
                <v-btn
                  @click="exportResultsForObserver(props.item)"
                  small
                  color="primary text-none ma-0"
                >
                  Export observer
                  <v-icon :size="20" class="ml-2">
                    arrow_downward
                  </v-icon>
                </v-btn>

                <!-- <v-btn
                  @click="getResultsForObserver(props.item)"
                  flat small
                  color="pa-0 text-none ma-0"
                >
                  <v-icon :size="20" class="ml-2">delete</v-icon>
                </v-btn> -->
              </td>
            </tr>
          </template>
          <!-- <template v-slot:expand="props"> -->
            <!-- @click="getResultsForObserver(experimentResult, i)" -->
            <!-- <v-layout v-for="(result, i) in props.results" :key="i" class="pa-2">
              <v-card flat>
                <v-card-text>{{ result.name }}</v-card-text>
              </v-card>
            </v-layout> -->
          <!-- </template> -->
        </v-data-table>
      </v-container>
    </v-card>
  </div>
</template>

<script>
import EventBus from '@/eventBus.js'
import Back from '@/components/Back'

export default {
  components: {
    Back
  },

  data () {
    return {
      loading: false,

      experiment: {
        id: null,
        title: null,
        experiment_type_id: null
      },

      experimentResults: []
    }
  },

  created () {
    this.loading = true

    this.getExperiment()
    this.getExperimentResults()
  },

  methods: {
    formatDate (dateTime) {
      var dateTimeSplit = dateTime.split(' ')
      var date = dateTimeSplit[0].split('-')
      var time = dateTimeSplit[1].split(':')

      const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sept', 'oct', 'nov', 'dec']

      return `${date[2]}. ${months[date[1] - 1]}. ${date[0]}, at ${time[0]}:${time[1]}`
    },

    getExperiment () {
      this.$axios.get(`/experiment/${this.$route.params.id}`)
        .then(response => {
          this.experiment = response.data
          this.loading = false
        })
        .catch(err => console.log(err))
    },

    getExperimentResults () {
      this.$axios.get(`/experiment-result/${this.$route.params.id}`)
        .then(response => {
          this.experimentResults = response.data
          this.loading = false
        })
        .catch(err => console.log(err))
    },

    getResultsForObserver (experimentResult, i) {
      this.$axios.get(`/paired-result/${experimentResult.id}`)
        .then(response => {
          // this.$set(this.experimentResults[i], 'results', response.data)
        })
        .catch(err => console.log(err))
    },

    exportResultsForObserver (experimentResult) {
      if (this.experiment.experiment_type_id === 1) {
        window.open(`${this.$API_URL}/paired-result/${experimentResult.id}/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 3) {
        window.open(`${this.$API_URL}/category-result/${experimentResult.id}/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 5) {
        window.open(`${this.$API_URL}/triplet-result/${experimentResult.id}/export`, '_blank')
      }
    },

    exportResultsForExperiment () {
      if (this.experiment.experiment_type_id === 1) {
        window.open(`${this.$API_URL}/${this.$route.params.id}/paired-result/all/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 3) {
        window.open(`${this.$API_URL}/${this.$route.params.id}/category-result/all/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 5) {
        window.open(`${this.$API_URL}/${this.$route.params.id}/triplet-result/all/export`, '_blank')
      }
    },

    wipeAllResults () {
      if (confirm('Do you want to delete ALL results data for this experiment?')) {
        this.$axios.delete(`/experiment-result/${this.$route.params.id}/wipe`).then(response => {
          if (response.data === 'deleted') {
            this.experimentResults = []

            EventBus.$emit('success', 'Experiment has been deleted successfully')
          } else {
            EventBus.$emit('error', 'Could not delete experiment')
          }
        })
      }
    }
  }
}
</script>

<style scoped lang="css">
  .qe-observer-results-panels {
    border: 1px solid #ddd;
    margin-bottom: 10px;
    cursor: pointer;
  }

  .qe-box {
    border-bottom: 1px solid #ddd;
  }
</style>
