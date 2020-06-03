<template>
  <div>
    <v-layout mb-5 mt-5 column>
      <h2 class="display-1 mb-1">
        Observer Results
      </h2>
      <h3 class="headline">
        {{ experiment.title }}
      </h3>
    </v-layout>

    <v-card>
      <Back>Back to all experiments</Back>

      <v-container pt-5 pl-5 pr-5 mt-1>
        <h2 class="headline mb-5 text-xs-center">Observers</h2>

        <v-layout align-center mb-3>
          <div>
            <v-btn
              v-if="experimentResults.length"
              :disabled="selected.length === 0"
              @click="exportResults()"
              :loading="exporting"
              color="primary"
              type="submit"
            >
              Export
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
          v-model="selected"
          :headers="headers"
          :items="experimentResults"
          item-key="id"
          select-all
          class="elevation-0"
          :expand="false"
          hide-default-footer
          :loading="loading"
          no-data-text=""
        >
          <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>

          <template v-slot:no-data>
            <div class="caption text-xs-center" v-if="loading === false">
              No observer data to show. No one has completed the experiment yet.
            </div>
          </template>

          <template v-slot:headers="props">
            <tr>
              <th>
                <v-checkbox
                  :input-value="props.all"
                  :indeterminate="props.indeterminate"
                  color="primary"
                  hide-details
                  @click.stop="toggleAll"
                ></v-checkbox>
              </th>
              <th
                v-for="header in props.headers"
                :key="header.text"
              >
                <!-- <v-icon small>arrow_upward</v-icon> -->
                {{ header.text }}
              </th>
            </tr>
          </template>
          <template v-slot:items="props">
            <tr :active="props.selected" @click="props.selected = !props.selected">
              <td>
                <v-checkbox
                  :input-value="props.selected"
                  color="primary"
                  hide-details
                ></v-checkbox>
              </td>
              <td>{{ props.item.user_id }}</td>
              <td>{{ props.item.id }}</td>
              <td>{{ formatDate(props.item.created_at) }}</td>
            </tr>
          </template>
        </v-data-table>

        <Plot v-if="experimentResults.length > 0"/>
      </v-container>
    </v-card>
  </div>
</template>

<script>
import EventBus from '@/eventBus.js'
import Back from '@/components/Back'
import Plot from '@/components/Plot'
import { formatDate } from '@/helpers.js'

export default {
  components: {
    Back,
    Plot
  },

  data () {
    return {
      loading: false,
      exporting: false,

      headers: [
        { text: 'Observer ID', value: 'name', sortable: false, desc: '' },
        { text: 'Session ID', value: 'session', align: 'left', sortable: false, desc: 'If the same observer has taken the experiment multiple times,<br> each attempt will have its own session ID.' },
        { text: 'Taken At', value: 'takenAt', sortable: false, desc: '' }
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

    toggleAll () {
      if (this.selected.length) this.selected = []
      else this.selected = this.experimentResults.slice()
    },

    getExperiment () {
      this.$axios.get(`/experiment/${this.$route.params.id}`)
        .then(response => {
          this.experiment = response.data
          this.loading = false

          if (this.experiment.experiment_type_id === 1) this.experimentTypeSlug = 'paired'
          if (this.experiment.experiment_type_id === 2) this.experimentTypeSlug = 'rank-order'
          if (this.experiment.experiment_type_id === 3) this.experimentTypeSlug = 'category'
          if (this.experiment.experiment_type_id === 5) this.experimentTypeSlug = 'triplet'
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
