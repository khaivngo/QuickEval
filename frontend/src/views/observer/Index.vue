<template>
  <v-row class="fill-height" style="padding-top: 0;">
    <v-col cols="6" style="border-right: 1px solid #ddd; overflow-y: auto; height: 91vh;" class="pa-12">
      <div>
        <v-text-field
          label="Find experiment"
          placeholder="Title, person, institute"
          outlined
          dense
          v-model="searchTerm"
          @keyup.enter="searchExperiments"
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
                    {{ experiment.version }}
                  </v-chip>
                </v-list-item-title>
                <v-list-item-subtitle>{{ experiment.user.name }}</v-list-item-subtitle>
              </v-list-item-content>
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
            <div v-if="Object.keys(active).length !== 0 && active.constructor === Object">
              <h2 class="text-center mb-6 headline">
                {{ active.title }}
              </h2>

              <p class="text-center mb-12 subtitle-1">
                {{ active.long_description }}
              </p>

              <div v-if="observerInputs.length > 0" mt-5>
                <div v-for="(observerInput, i) in observerInputs" :key="i" class="mb-4">
                  <v-text-field
                    :label="observerInput.meta"
                    v-model="observerInput.answer"
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
  </v-row>
</template>

<script>
export default {
  data: () => ({
    active: {},
    experiments: [],
    observerInputs: [],
    searchTerm: '',
    prefetch: false,
    showActive: false,
    loading: false
  }),

  computed: {
    // selected () {
    //   if (Object.keys(this.active).length === 0 && this.active.constructor === Object) {
    //     return undefined
    //   }

    //   const id = this.active.id

    //   return this.experiments.find(experiment => experiment.id === id)
    // },

    isActive () {
      return Object.keys(this.active).length !== 0 && this.active.constructor === Object
    }
  },

  watch: {
    active (exp) {
      this.$axios.get(`/experiment/${exp.id}/observer-metas`)
        .then(response => { this.observerInputs = response.data })
        .catch(err => console.warn(err))
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
        return this.$axios.get('/experiment/' + this.$route.params.id + '/public').then((response) => {
          this.experiments.push(response.data)

          this.active = response.data
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
        inputs: this.observerInputs
      })
    },

    async startExperiment () {
      this.prefetch = true

      const experimentResult = await this.$axios.post('/experiment-result/create', { experimentId: this.active.id })
      localStorage.setItem('experimentResult', experimentResult.data.id)

      if (experimentResult.data) {
        if (this.observerInputs.length > 0) {
          await this.saveObserverInputs(experimentResult.data.id)
        }

        this.prefetch = false

        // Onward!
        this.$router.push(`/experiment/${this.active.experiment_type_id}/${this.active.id}`)
      }
    },

    searchExperiments () {
      if (this.searchTerm === '') {
        this.$axios.get(`/experiment/public`).then((response) => {
          console.log(response)
          this.experiments = response.data
        })
        return
      }

      this.$axios.get(`/experiment/${this.searchTerm}/search/public`).then((response) => {
        console.log(response)
        this.experiments = response.data
      })
    }
  }
}
</script>
