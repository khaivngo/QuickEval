<template>
  <v-row class="fill-height" no-gutters>
    <v-col cols="3" class="fill-height" style="background: #ddd; max-width: 256px;">
      <!-- this navigation drawer is position fixed, so the <v-col> parent must be set to the same width -->
      <v-navigation-drawer permanent app style="z-index: 1; margin-top: 64px; margin-left: 256px; max-width: 300px;">
        <v-list-item class="mt-2">
          <v-list-item-content>
            <v-list-item-title>
              <v-btn text class="pl-2" :to="'/scientist/experiments/create'">
                <v-icon color="primary" class="pa-0">mdi-plus</v-icon> Create new
              </v-btn>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list
          dense
        >
          <v-list-item-group v-model="active" color="primary">
            <v-list-item
              v-for="(experiment, i) in experiments"
              :key="i"
              link
              @click="$router.push(`/scientist/experiments/view/${experiment.id}`)"
              class="pl-8"
            >
              <v-list-item-content>
                <v-list-item-title>
                  {{ experiment.title }}
                  <v-chip v-if="experiment.version > 1" disabled text-color="#222" small class="ml-2">
                    version {{ experiment.version }}
                  </v-chip>
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ experiment.completed_results_count }} completed
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>

        <v-progress-linear v-slot:progress indeterminate class="ma-0" :height="2" v-if="loading"></v-progress-linear>
        <div class="caption pa-4" v-if="loading === false && experiments.length === 0">
          You have no experiments. Yet...
        </div>
      </v-navigation-drawer>
    </v-col>

    <v-col class="pr-12 pl-12 pt-6">
      <v-row>
        <router-view :key="$route.params.id || ''"/>
      </v-row>
    </v-col>
  </v-row>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      experiments: [],

      selected: {},

      active: null,

      loading: false
    }
  },

  // watch: {
  //   $route (to, from) {
  //     // react to route changes...
  //     // this.active = to.params.id
  //     // console.log(from)
  //     // this.experiment.
  //   }
  // },

  created () {
    this.getUsersExperiments()

    EventBus.$on('experiment-created', (payload) => {
      this.experiments.unshift(payload)
    })
  },

  methods: {
    getUsersExperiments () {
      this.loading = true

      this.$axios.get('/experiments').then(response => {
        this.experiments = response.data

        let activeIndex = this.experiments.findIndex(exp => exp.id === parseInt(this.$route.params.id))
        this.active = activeIndex

        this.loading = false
      }).catch(() => {
        this.loading = false
      })
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
