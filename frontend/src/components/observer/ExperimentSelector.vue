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
    <v-card flat>
      <!-- <v-card-title class="headline">
        Select Experiment
      </v-card-title> -->

      <v-layout justify-space-between pa-3>
        <!-- <v-layout pa-4>
        </v-layout> -->

        <v-flex xs6>
          <div class="mb-5 mr-2 ml-2">
            <v-text-field
              v-model="searchTerm"
              @keyup.enter="findExperiment"
              label="Search"
              prepend-inner-icon="search"
            ></v-text-field>
          </div>

          <v-treeview
            :active.sync="active"
            :items="items"
            :load-children="fetchUsers"
            :open.sync="open"
            activatable
            active-class="primary--text"
            class="grey lighten-5"
            open-on-click
            transition
            item-text="title"
          >
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
              <!-- <v-divider></v-divider> -->
              <v-layout
                tag="v-card-text"
                text-xs-left
                wrap
              >
                <!-- <v-flex tag="strong" xs5 text-xs-right mr-3 mb-2>Contact:</v-flex>
                <v-flex>
                  <a :href="`mailto: ${selected.email}`">
                    {{ selected.email }}
                  </a>
                </v-flex> -->
                <!-- <v-flex tag="strong" xs5 text-xs-right mr-3 mb-2>University:</v-flex>
                <v-flex>{{ selected.company.name }}</v-flex> -->
              </v-layout>

              <!-- <v-layout> -->
                <v-flex>
                  <v-btn @click="startExperiment" color="success" :loading="prefetch">
                    Start experiment
                  </v-btn>
                </v-flex>
              <!-- </v-layout> -->
            </v-card>
          </v-scroll-y-transition>
        </v-flex>
      </v-layout>
    </v-card>
  </div>
</template>

<script>
const pause = ms => new Promise(resolve => setTimeout(resolve, ms))

export default {
  created () {
    this.$axios.get('/experiments/all').then((response) => {
      this.experiments = response.data
    })
  },

  data: () => ({
    experiments: [],
    active: [],
    open: [],
    users: [
      { id: 23, name: 'CID:IQ', company: { name: 'NTNU' }, email: 'Sincere@april.biz', description: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' },
      { id: 55, name: 'Image quality experiment', company: { name: 'Høgskolen i Lillehammer' }, email: 'john@edu.com', description: 'The english language consists of letters and words.' },
      { id: 4535, name: 'Hello world', company: { name: 'NTNU' }, email: 'Sincere@april.biz', description: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' },
      { id: 3535, name: 'Assessment of image quality', company: { name: 'Høgskolen i Lillehammer' }, email: 'john@edu.com', description: 'The english language consists of letters and words.' },
      { id: 2424, name: 'CID:IQ 2', company: { name: 'NTNU' }, email: 'Sincere@april.biz', description: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' },
      { id: 7657, name: 'Experiment experiment', company: { name: 'Høgskolen i Lillehammer' }, email: 'john@edu.com', description: 'The english language consists of letters and words.' },
      { id: 868, name: 'Not sure what to write', company: { name: 'NTNU' }, email: 'Sincere@april.biz', description: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' },
      { id: 4646, name: 'Image quality experiment', company: { name: 'Høgskolen i Lillehammer' }, email: 'john@edu.com', description: 'The english language consists of letters and words.' },
      { id: 234, name: 'CID:IQ', company: { name: 'NTNU' }, email: 'Sincere@april.biz', description: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' },
      { id: 1313, name: 'Image quality experiment', company: { name: 'Høgskolen i Lillehammer' }, email: 'john@edu.com', description: 'The english language consists of letters and words.' }
    ],
    prefetch: false,

    searchTerm: ''
  }),

  computed: {
    items () {
      return [
        {
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

  methods: {
    async fetchUsers (item) {
      // Remove in 6 months and say
      // you've made optimizations! :)
      await pause(500)

      return this.$axios.get('/experiments/all')
        .then(res => res.data.json())
        .then(json => (item.children.push(...json)))
        .catch(err => console.warn(err))
    },

    async startExperiment () {
      this.prefetch = true
      await pause(1000)
      this.prefetch = false

      this.$router.push('/experiment')
      // new Promise((resolve) => {
      //   resolve('Experiment has begun!')
      // }).then(val => {
      //   console.log(val)
      // })
    },

    findExperiment () {
      //
    }
  }
}
</script>

<style scoped lang="scss">
  .step-title {
    align-self: center;
  }
</style>
