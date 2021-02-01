<template>
  <div class="d-flex flex-grow-1">
    <div class="qe-nav-drawer">
      <v-list
        dense
        class="qe-drawer-2"
      >
        <v-list-item class="mt-1 mb-2">
          <v-list-item-content>
            <v-list-item-title>
              <v-btn text class="pl-2" :to="'/scientist/experiments/create'">
                <v-icon color="primary" class="pa-0">mdi-plus</v-icon> Create new
              </v-btn>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>

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
                {{ experiment.completed_results_count }}<span v-if="!experiment.hasOwnProperty('completed_results_count')">0</span> completed
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>

      <v-progress-linear v-slot:progress indeterminate class="ma-0" :height="2" v-if="loading"></v-progress-linear>

      <v-list-item v-if="loading === false && experiments.length === 0">
        <v-list-item-content>
          <v-list-item-title>
            <div class="caption ma-4">
              You have no experiments. Yet...
            </div>
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </div>

    <!-- the menu above is position fixed, so we put a "mold" below -->
    <div style="flex: 0 0 270px; height: 20px;"></div>

    <router-view :key="$route.params.id || ''"/>
  </div>
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

  created () {
    this.getUsersExperiments()

    EventBus.$on('experiment-created', (payload) => {
      this.experiments.unshift(payload)
      let index = this.experiments.findIndex(exp => exp.id === payload.id)
      this.active = index
    })

    EventBus.$on('experiment-deleted', (payload) => {
      let exp = this.experiments.findIndex(exp => exp.id === payload.id)
      this.experiments.splice(exp, 1)

      this.active = null
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

<style lang="scss">
  /* NOTICE: the 'scoped' slot is omitted because it makes overriding Vuetify styles possible (for whatever reason) */

  $scrollbarBG: #CFD8DC;
  $thumbBG: #90A4AE;

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

  .qe-nav-drawer {
    z-index: 1;
    padding-top: 64px;
    width: 270px;
    overflow-y: auto;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 240px;
    border-right: 1px solid #ddd;
    background: #fff;
  }
  @media (max-width: 1150px) {
    .qe-nav-drawer {
      left: 58px;
    }
  }
</style>
