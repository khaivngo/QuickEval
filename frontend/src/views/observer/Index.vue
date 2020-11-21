<template>
  <v-row class="fill-height" style="padding-top: 0;">
    <v-col cols="6" style="border-right: 1px solid #ddd; overflow-y: auto; height: 91vh;" class="pa-12">
      <div class="mb-2">
        <v-text-field
          label="Find experiment"
          placeholder="Title, person, type"
          outlined
          dense
          v-model="searchTerm"
          @keyup.enter="searchExperiments"
          :loading="searching"
        ></v-text-field>
      </div>

      <div>
        <v-list>
          <!-- v-model="active.id" -->
          <v-list-item-group
            color="primary"
          >
            <v-list-item
              v-for="(experiment, i) in experiments"
              :key="i"
              @click="setActive(experiment)"
              two-line
            >
              <v-list-item-content>
                <v-list-item-title>
                  {{ experiment.title }}
                  <v-chip v-if="experiment.version > 1" disabled text-color="#222" small class="ml-2">
                    version {{ experiment.version }}
                  </v-chip>
                </v-list-item-title>
                <v-list-item-subtitle>{{ experiment.user.name }}</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item v-if="experiments.length === 0 && loading === false">
              <v-list-item-title>Could not find any experiments</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>

        <v-container>
          <v-row
            v-if="loading"
            class="fill-height"
            align-content="center"
            justify="center"
          >
            <v-col
              class="subtitle-1 text-center"
              cols="12"
            >
              Getting experiments
            </v-col>
            <v-col cols="6">
              <v-progress-linear
                indeterminate
                rounded
                height="6"
              ></v-progress-linear>
            </v-col>
          </v-row>
        </v-container>
      </div>
    </v-col>

    <v-col cols="6" class="justify-center pa-12">
      <v-row align="center" justify="center" class="fill-height">

        <div v-if="isActive">
          <v-scroll-y-transition mode="out-in">
            <div
              v-if="Object.keys(active).length !== 0 && active.constructor === Object"
            >
              <h2 class="text-center mb-6 headline">
                {{ active.title }}
              </h2>

              <p class="text-center mb-12 subtitle-1">
                {{ active.long_description }}
              </p>

              <div v-if="active.observer_metas.length > 0" mt-5>
                <div v-for="(input, i) in active.observer_metas" :key="i" class="mb-2">
                  <v-text-field
                    :label="input.observer_meta.meta"
                    v-model="input.observer_meta.answer"
                    outlined
                    dense
                  ></v-text-field>
                </div>
              </div>

              <v-row class="mt-6 mb-6 mr-0 ml-0 pa-0" justify="center">
                <v-btn @click="startExperiment()" color="success" :loading="prefetch">
                  Start experiment
                </v-btn>
              </v-row>
            </div>
          </v-scroll-y-transition>
        </div>
        <div v-else>
          <h3 class="text-center mb-6 headline grey--text text--lighten-1 font-weight-light">
            <v-icon class="grey--text text--lighten-1 font-weight-light">
              mdi-arrow-left
            </v-icon>
            Select Experiment
          </h3>
        </div>
      </v-row>
    </v-col>

    <v-scale-transition>
      <IshiharaTest
        v-if="showIshihara"
        :experimentId="this.active.id"
        @finished="ishiharaFinished"
        @aborted="ishiharaAborted"
      />
    </v-scale-transition>
  </v-row>
</template>

<script>
import IshiharaTest from '@/components/IshiharaTest'

export default {
  components: {
    IshiharaTest
  },

  data: () => ({
    active: {},
    experiments: [],
    searchTerm: '',
    searching: false,
    prefetch: false,
    showActive: false,
    loading: false,
    showIshihara: false
  }),

  computed: {
    isActive () {
      return Object.keys(this.active).length !== 0 && this.active.constructor === Object
    }
  },

  created () {
    this.loading = true
    this.fetchExperiments()
  },

  methods: {
    setActive (experiment) {
      this.active = {} // empty the active experiment, so that we can run the transition animation again
      this.active = experiment
    },

    async fetchExperiments () {
      // if the url contains an ID we fetch just that specific experiment
      // this way the researcher can send a direct link to participants
      if (this.$route.params.id !== undefined) {
        return this.$axios.get(`/experiment/${this.$route.params.id}`).then((response) => {
          if (response.data) {
            this.experiments.push(response.data)
            this.active = response.data
          }
          this.loading = false
        }).catch(() => {
          this.loading = false
        })
      } else {
        return this.$axios.get('/experiments/public').then((response) => {
          this.experiments = response.data
          this.loading = false
        }).catch(() => {
          this.loading = false
        })
      }
    },

    async saveObserverInputs (id) {
      return this.$axios.post('/result-observer-metas', {
        resultObserverMetaId: id,
        inputs: this.active.observer_metas
      })
    },

    async startExperiment () {
      this.prefetch = true

      const experimentResult = await this.$axios.post('/experiment-result/create', { experimentId: this.active.id })
      localStorage.setItem(`${this.active.id}-experimentResult`, experimentResult.data.id)

      if (experimentResult.data) {
        if (this.active.observer_metas.length > 0) {
          await this.saveObserverInputs(experimentResult.data.id)
        }

        this.prefetch = false

        // Onward!
        if (this.active.ishihara && this.active.ishihara === 1) {
          this.showIshihara = true
        } else {
          this.$router.push(`/experiment/${this.active.experiment_type_id}/${this.active.id}`)
        }
      }
    },

    ishiharaFinished () {
      this.$router.push(`/experiment/${this.active.experiment_type_id}/${this.active.id}`)
    },

    ishiharaAborted () {
      this.showIshihara = false
    },

    searchExperiments () {
      this.searching = true

      // empty string values is not allowed in the GET parameter, so we send 'all' instead
      let searchTerm = this.searchTerm !== '' ? this.searchTerm : 'all'
      this.$axios.get(`/experiment/${searchTerm}/search/public`).then((response) => {
        this.active = {}
        this.experiments = response.data
        this.searching = false
      }).catch(() => {
        this.searching = false
      })
    }
  }
}
</script>
