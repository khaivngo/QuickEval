<template>
  <div>
    <v-layout mb-5>
      <h2 class="display-1">
        Results Data
      </h2>
    </v-layout>

    <v-card>
      <v-container class="ma-0 pa-2" style="border-bottom: 1px solid #ccc;">
        <v-layout justify-space-between align-center>
          <v-btn outline fab small @click="$router.back()">
            <v-icon>arrow_back</v-icon>
          </v-btn>

          <v-btn outline color="error text-none mt-0 mb-0 mr-2" @click="wipeAllResults">
            Wipe ALL data <v-icon :size="20" class="ml-2">delete</v-icon>
          </v-btn>
        </v-layout>
      </v-container>
      <v-container>
        <v-layout>
          <h4 class="display-1">
            {{ experiment.title }}
          </h4>
        </v-layout>

        <!-- <v-layout>
          <div>
            System details
          </div>
        </v-layout> -->

        <v-layout column class="mt-4">
          <!-- <p class="subheading">
            Type: {{ experiment.experiemnt_type_id }}
          </p> -->

          <p>{{ experiment.short_description }}</p>

          <InviteLink :code="experiment.id"/>
        </v-layout>

        <v-container fluid pa-0 mt-5>
          <v-layout justify-space-between align-center mb-3>
            <h2 class="title">
              Observer Raw Data
            </h2>

            <v-btn
              @click="exportResultsForExperiment()"
              outline color="primary text-none ma-0"
            >
              Export ALL data <v-icon :size="20" class="ml-2">arrow_downward</v-icon>
            </v-btn>
          </v-layout>

          <v-data-table
            :headers="[
              { text: 'Observer ID', value: 'name', align: 'left', sortable: false },
              { text: 'Taken At', value: 'takenAt' },
              { text: 'Export raw data', value: 'export', align: 'right', sortable: false }
            ]"
            :items="experimentResults"
            no-data-text="There is no observer data yet."
            :expand="false"
            item-key="id"
            hide-actions
            :loading="loading"
          >
            <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
            <template v-slot:items="props">
              <tr @click="props.expanded = !props.expanded">
                <td>{{ props.item.id }}</td>
                <td>{{ props.item.created_at }}</td>
                <td class="text-xs-right">
                  <v-btn
                    @click="exportResultsForObserver(props.item)"
                    outline small
                    color="primary text-none ma-0"
                  >
                    Export
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
            <template v-slot:expand="props">
              <!-- @click="getResultsForObserver(experimentResult, i)" -->
              <!-- <v-layout v-for="(result, i) in props.results" :key="i" class="pa-2">
                <v-card flat>
                  <v-card-text>Peek-a-boo! {{ result.name }}</v-card-text>
                  <v-img :src="$UPLOADS_FOLDER + results.path" width="400"></v-img>
                </v-card>
              </v-layout> -->
            </template>
          </v-data-table>

        </v-container>

      </v-container>
    </v-card>
  </div>
</template>

<script>
import InviteLink from '@/components/scientist/InviteLink'
import EventBus from '@/eventBus.js'

export default {
  components: {
    InviteLink
  },

  data () {
    return {
      loading: false,

      experiment: {
        id: null,
        title: null,
        type: null,
        short_description: null,
        is_public: null,
        inviteCode: null
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
          console.log(response)
        })
        .catch(err => console.log(err))
    },

    exportResultsForObserver (experimentResult) {
      window.open(`${this.$API_URL}/paired-result/${experimentResult.id}/export`, '_blank')
    },

    exportResultsForExperiment () {
      window.open(`${this.$API_URL}/${this.$route.params.id}/paired-result/all/export`, '_blank')
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
