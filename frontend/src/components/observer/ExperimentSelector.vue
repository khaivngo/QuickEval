<template>
  <div>
    <div>
      <v-layout mb-5 mt-5>
        <h2 class="display-1">
          Select Experiment
        </h2>
      </v-layout>

      <!-- <v-btn outline @click="findPostsByString">
        søk
      </v-btn> -->
    </div>
    <v-card>
      <!-- <v-card-title class="headline">
        Select Experiment
      </v-card-title> -->

      <v-layout justify-space-between>
        <v-flex xs6 style="background-color: #F7F7F7;">
          <div class=" mr-2 ml-2 pt-3 pr-5 pl-4">
            <v-text-field
              v-model="searchTerm"
              @keyup.enter="findExperiment"
              label="Search"
              prepend-inner-icon="search"
            ></v-text-field>
          </div>

          <v-treeview
            class="pa-3 mb-5"
            :active.sync="active"
            :items="items"
            :load-children="fetchExperiments"
            :open.sync="open"
            active-class="font-weight-black"
            transition
            activatable
            open-on-click
            item-text="title"
          >
            <!-- active-class="primary--text font-weight-black" -->
            <template v-slot:prepend="{ item, active }">
              <v-icon
                v-if="!item.children"
                :color="active ? 'primary' : ''"
              >
                mdi-account
              </v-icon>
            </template>
          </v-treeview>
        </v-flex>
        <v-flex
          d-flex
          xs6
          pa-5
          mb-5
          text-xs-center
        >
          <v-scroll-y-transition mode="out-in">
            <div
              v-if="!selected"
              class="step-title title grey--text text--lighten-1 font-weight-light"
            >
              Select Experiment
            </div>
            <v-card v-else :key="selected.id" class="pt-4 mx-auto" flat max-width="400">
              <v-card-text>
                <h3 class="headline mb-2">
                  {{ selected.title }}
                </h3>
              </v-card-text>

              <v-layout
                tag="v-card-text"
                wrap
              >
                <!-- text-xs-left -->
                <v-flex>{{ selected.short_description }}</v-flex>
              </v-layout>

              <!-- <v-divider></v-divider> -->

              <v-layout column v-if="observerInputs.length > 0" mt-5>
                <v-flex v-for="(observerInput, i) in observerInputs" :key="i">
                  <v-text-field
                    :label="observerInput.meta"
                    v-model="observerInput.answer"
                  ></v-text-field>
                </v-flex>
              </v-layout>

               <v-layout mt-4>
                <v-flex>
                  <v-btn @click="startExperiment(selected)" color="success" :loading="prefetch">
                    Start experiment
                  </v-btn>
                </v-flex>
              </v-layout>

            </v-card>
          </v-scroll-y-transition>
        </v-flex>
      </v-layout>
    </v-card>
  </div>
</template>

<script>
export default {
  data: () => ({
    active: [],
    open: [1],

    prefetch: false,
    experiments: [],
    observerInputs: [],
    searchTerm: ''
  }),

  computed: {
    items () {
      return [
        {
          id: 1,
          title: 'Experiments',
          children: this.experiments
        }
      ]
    },

    selected () {
      if (!this.active.length) return undefined

      const id = this.active[0]

      return this.experiments.find(experiment => experiment.id === id)
    }
  },

  watch: {
    selected (exp) {
      this.$axios.get(`/experiment/${exp.id}/observer-metas`)
        .then(response => { this.observerInputs = response.data })
        .catch(err => console.warn(err))
    }
  },

  methods: {
    async fetchExperiments (item) {
      // if the url contains an ID we fetch just that specific experiment
      // this way the researcher can send a direct link to participants
      if (this.$route.params.id !== undefined) {
        return this.$axios.get('/experiment/' + this.$route.params.id + '/public').then((response) => {
          this.experiments.push(response.data)

          this.active.push(response.data.id)
        })
      } else {
        return this.$axios.get('/experiments/public').then((response) => {
          this.experiments = response.data
        })
      }
    },

    async saveObserverInputs () {
      return this.$axios.post('/experiment-observer-meta-result', this.observerInputs)
    },

    async startExperiment (selected) {
      this.prefetch = true

      const experimentResult = await this.$axios.post('/experiment-result/create', { experimentId: selected.id })
      localStorage.setItem('experimentResult', experimentResult.data.id)

      if (experimentResult.data) {
        if (this.observerInputs.length > 0) {
          await this.saveObserverInputs()
        }

        this.prefetch = false

        // Onward!
        this.$router.push(`/experiment/${selected.experiment_type_id}/${selected.id}`)
      }
    }
  }
}
</script>

<style scoped lang="scss">
  .step-title {
    align-self: center;
  }
</style>
