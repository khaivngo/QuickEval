<template>
  <div>
    <v-layout mb-5 mt-5>
      <h2 class="display-1 mr-5">
        Your Experiments
      </h2>

      <!-- <v-btn color="success" class="ma-0" :to="'/scientist/experiments/create'" exact>
        Create new
      </v-btn> -->
      <v-btn color="success" class="ma-0" :to="'/scientist/experiments/create'" exact>
        Create new
      </v-btn>
    </v-layout>

    <!-- <v-layout justify-end mb-3>
      <v-btn color="success" class="ma-0" :to="'/scientist/experiments/create'" exact>
        Create new
      </v-btn>
    </v-layout> -->

    <div class="mt-3">
      <!-- <v-layout class="pa-4 ma-0" style="border-bottom: 1px solid #ddd;">
        <v-flex v-for="(header, i) in headers" :key="i">
          <h6 class="caption">
            {{ header.text }}
          </h6>
        </v-flex>
      </v-layout> -->

      <v-progress-linear v-slot:progress indeterminate class="ma-0" :height="2" v-if="loading"></v-progress-linear>
      <div class="caption" v-if="loading === false && experiments.length === 0">
        You have no experiments. Yet...
        <!-- <span style="font-size: 20px;">&#x261D;</span> U+1F9EA U+1F52C -->
      </div>

      <v-card fluid v-for="(experiment, i) in experiments" :key="i" class="pt-3 pb-3 pr-4 pl-4 mb-3">
        <v-layout align-center>
          <v-flex grow class="align-center">
            <v-layout align-center>
              <h3 class="title">
                {{ experiment.title }}
              </h3>
              <v-chip v-if="experiment.version > 1" disabled text-color="#222" small class="ml-2">
                version {{ experiment.version }}
              </v-chip>
            </v-layout>
          </v-flex>
          <v-flex shrink>
            <v-layout align-center justify-center class="qe-public-switch">
              <v-flex shrink class="mr-1">
                <div class="caption" :class="(experiment.is_public === 0 || experiment.is_public === false) ? 'font-weight-bold' : ''">
                  hidden
                </div>
              </v-flex>
              <v-flex shrink>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-switch
                      v-on="on"
                      class="ma-0 pa-0"
                      v-model="experiment.is_public"
                      color="success"
                      @change="visibility(experiment)"
                    >
                    </v-switch>
                  </template>
                  <span>Toggle public visibility of experiment.</span>
                </v-tooltip>
              </v-flex>

              <v-flex shrink class="ml-1">
                <div class="caption" :class="(experiment.is_public === 1 || experiment.is_public === true) ? 'font-weight-bold' : ''">
                  public
                </div>
              </v-flex>

              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn :to="`/scientist/experiments/edit/${experiment.id}`" v-on="on" icon class="ma-0 mr-1 ml-5">
                    <v-icon>edit</v-icon>
                  </v-btn>
                </template>
                <span>Edit experiment</span>
              </v-tooltip>

              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn @click="destroy(experiment, i)" v-on="on" icon class="ma-0">
                    <v-icon>delete</v-icon>
                  </v-btn>
                </template>
                <span>Delete experiment</span>
              </v-tooltip>
            </v-layout>
          </v-flex>
        </v-layout>
        <v-layout mt-5 space-between align-center>
          <v-flex>
            <!-- <div class="caption">Sharable:</div> -->
            <Clipboard :url="`${$DOMAIN}/observer/${experiment.id}`"/>
          </v-flex>
          <v-flex shrink>
            <!-- <v-btn :to="`/scientist/experiments/view/${experiment.id}`" flat icon>
              <v-icon>bar_chart</v-icon>
            </v-btn> -->

            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn :to="`/scientist/experiments/view/${experiment.id}`" v-on="on" color="primary" class="ml-0">
                  <v-icon class="mr-2">bar_chart</v-icon>results
                </v-btn>
              </template>
              <span>View observer results</span>
            </v-tooltip>

          </v-flex>
        </v-layout>
      </v-card>
    </div>
  </div>
</template>

<script>
import EventBus from '@/eventBus'
import Clipboard from '@/components/Clipboard'

export default {
  components: {
    Clipboard
  },

  data () {
    return {
      headers: [
        { value: 'title',   sortable: false,  text: 'Title' },
        { value: 'results', sortable: false,  text: 'Results' },
        { value: 'public',  sortable: false,  text: 'Visible to the public' },
        { value: 'invite',  sortable: false,  text: 'Shareable link to take part in experiment' },
        { value: 'edit',    sortable: false,  text: 'Actions' }
      ],

      experiments: [],

      loading: false
    }
  },

  created () {
    this.getUsersExperiments()
  },

  methods: {
    getUsersExperiments () {
      this.loading = true

      this.$axios.get('/experiments').then(response => {
        this.experiments = response.data

        this.loading = false
      }).catch(() => {
        this.loading = false
      })
    },

    visibility (exp) {
      this.$axios.patch('/experiment/' + exp.id + '/visibility', {
        is_public: exp.is_public
      }).then(response => {
        if (response.data.is_public) {
          EventBus.$emit('success', 'Experiment is visible to the public')
        } else {
          EventBus.$emit('info', 'Experiment is hidden from the public.')
        }
      })
    },

    destroy (exp, arrayIndex) {
      if (confirm('Do you want to delete the experiment? You will no longer be able to retrive observer data.')) {
        this.$axios.delete(`/experiment/${exp.id}`).then(response => {
          if (response.data === 'deleted_experiment') {
            this.experiments.splice(arrayIndex, 1)

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

<style lang="css">
  /* NOTICE: the 'scoped' slot is omitted because it makes overriding Vuetify styles possible (for whatever reason) */

  /* Vuetify overriding styles, remove spacing beneath the public/hidden switches */
  .qe-public-switch .v-input__slot {
    margin-bottom: 0 !important;
  }

  .qe-public-switch .v-input .v-input__control .v-messages {
    display: none !important;
    height: 0px !important;
  }

  .qe-public-switch .v-input--selection-controls__input {
    margin-right: 0px !important;
  }

  /*  */
  .qe-experiment-link-container {
    display: flex;
    align-items: center;
    height: 100%;
  }
</style>
