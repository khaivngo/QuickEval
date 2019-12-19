<template>
  <div>
    <v-layout mb-5 mt-5 column>
      <h2 class="display-1">
        Observer Results
      </h2>
      <h3 class="headline">
        {{ experiment.title }}
      </h3>
    </v-layout>

    <v-card>
      <Back>Back to all experiments</Back>

      <v-container pt-5 mt-1>
        <h2 class="mb-5 text-xs-center">Observers</h2>

        <v-layout justify-end align-center mb-3>
          <!-- <h2 class="title"> -->
            <!-- Observer Raw Data -->
          <!-- </h2> -->

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

            <v-btn
              v-if="observerMetas.length"
              @click="exportObserverMetasForExperiment()"
              color="primary text-none ma-0 ml-2"
            >
              Export ALL demographics
              <v-icon :size="20" class="ml-2">
                arrow_downward
              </v-icon>
            </v-btn>
          </div>
        </v-layout>

        <v-data-table
          :headers="[
            { text: 'Observer ID', value: 'name', align: 'left', sortable: false, desc: '' },
            {
              text: 'Session ID', value: 'session', align: 'left', sortable: false,
              desc: 'If the same observer has taken the experiment multiple times,<br> each attempt will have its own session ID.'
            },
            { text: 'Taken At', value: 'takenAt', sortable: false, desc: '' },
            { text: 'Export raw data', value: 'export', align: 'right', sortable: false, desc: '' }
          ]"
          :items="experimentResults"
          no-data-text=""
          :expand="false"
          item-key="id"
          hide-actions
          :loading="loading"
        >
          <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
          <template slot="headerCell" slot-scope="props">
            {{ props.header.text }}
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <span v-on="on">
                  <v-icon small v-if="props.header.value === 'session'">
                    help
                  </v-icon>
                </span>
              </template>
              <div class="pl-2 pr-2 body-1" v-html="props.header.desc"></div>
            </v-tooltip>
          </template>
          <template v-slot:no-data>
            <div class="caption text-xs-center" v-if="loading === false">
              No observer data to show. No one has completed the experiment yet.
            </div>
          </template>
          <template v-slot:items="props">
            <tr @click="props.expanded = !props.expanded">
              <td>{{ props.item.user_id }}</td>
              <td>{{ props.item.id }}</td>
              <td>{{ formatDate(props.item.created_at) }}</td>
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
                  @click="exportObserverMetasForObserver(props.item)"
                  small
                  color="primary text-none ma-0 ml-2"
                >
                  Export observer demographics
                  <v-icon :size="20" class="ml-2">
                    arrow_downward
                  </v-icon>
                </v-btn> -->
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

      <v-data-table
        :headers="[
          { text: 'Title', value: 'title', sortable: false, desc: '' },
          { text: 'Low CI limit', value: 'lowCiLimit', sortable: false, desc: '' },
          { text: 'Mean z-score', value: 'meanZscore', align: 'right', sortable: false, desc: '' },
          { text: 'High CI limit', value: 'highCiLimit', sortable: false },
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
            <td>{{ props.item.user_id }}</td>
            <td>{{ props.item.id }}</td>
            <td class="text-xs-right">
            </td>
          </tr>
        </template>
      </v-data-table>

      <Plot/>
    </v-card>
  </div>
</template>

<script>
import EventBus from '@/eventBus.js'
import Back from '@/components/Back'
import Plot from '@/components/Plot'
import { formatDate } from '@/helpers.js'
// import '@/math.js'

export default {
  components: {
    Back,
    Plot
  },

  data () {
    return {
      loading: false,

      experiment: {
        id: null,
        title: null,
        experiment_type_id: null
      },

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

    // getResultsForObserver (experimentResult, i) {
    //   this.$axios.get(`/paired-result/${experimentResult.id}`)
    //     .then(response => {
    //       // this.$set(this.experimentResults[i], 'results', response.data)
    //     })
    //     .catch(err => console.log(err))
    // },

    exportResultsForObserver (experimentResult) {
      if (this.experiment.experiment_type_id === 1) {
        window.open(`${this.$API_URL}/paired-result/${experimentResult.id}/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 2) {
        window.open(`${this.$API_URL}/rank-order-result/${experimentResult.id}/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 3) {
        window.open(`${this.$API_URL}/category-result/${experimentResult.id}/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 5) {
        window.open(`${this.$API_URL}/triplet-result/${experimentResult.id}/export`, '_blank')
      }
    },

    exportResultsForExperiment () {
      // window.open(`${this.$API_URL}/${this.$route.params.id}/${this.experiment.experiment_type_id}/all/export`, '_blank')

      if (this.experiment.experiment_type_id === 1) {
        window.open(`${this.$API_URL}/${this.$route.params.id}/paired-result/all/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 2) {
        window.open(`${this.$API_URL}/${this.$route.params.id}/rank-order-result/all/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 3) {
        window.open(`${this.$API_URL}/${this.$route.params.id}/category-result/all/export`, '_blank')
      } else if (this.experiment.experiment_type_id === 5) {
        window.open(`${this.$API_URL}/${this.$route.params.id}/triplet-result/all/export`, '_blank')
      }
    },

    exportObserverMetasForExperiment () {
      window.open(`${this.$API_URL}/experiment-observer-meta-result/${this.$route.params.id}/export`, '_blank')
    },

    exportObserverMetasForObserver (user) {
      window.open(`${this.$API_URL}/experiment-observer-meta-result/${this.$route.params.id}/${user.user_id}/export`, '_blank')
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
