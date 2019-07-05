<template>
  <div>
    <v-stepper v-model="currentLevel" alt-labels non-linear>
      <v-stepper-header class="elevation-0">
        <template v-for="(item, i) in steps">
          <v-stepper-step :step="i + 1" :key="i" editable>
            {{ item.title }}
            <!-- <small v-if="item.subText">
              {{ item.subText }}
            </small> -->
          </v-stepper-step>

          <v-divider v-if="i !== steps.length - 1" :key="i + 100"/>
        </template>
      </v-stepper-header>

      <v-stepper-items class="no-transition">
        <v-stepper-content step="1">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ steps[0].title }}</h2>
            <v-form>
              <v-flex xs12 sm12 md12 xl12>
                <v-layout align-center>
                  <v-flex grow>
                    <v-text-field
                      class="mt-3"
                      v-model.trim="form.basicDetails.title"
                      label="Experiment Name"
                    ></v-text-field>
                  </v-flex>
                  <v-flex shrink style="opacity: 0;">
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon color="grey lighten-1">info</v-icon>
                        </v-btn>
                      </template>
                      <span></span>
                    </v-tooltip>
                  </v-flex>
                </v-layout>

                <v-layout align-center>
                  <v-flex grow>
                    <v-text-field
                      class="mt-3"
                      v-model.trim="form.basicDetails.shortDescription"
                      label="Short Description"
                    ></v-text-field>
                  </v-flex>
                  <v-flex shrink>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon color="grey lighten-1">help_outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Describe the essence of the experiment.</span>
                    </v-tooltip>
                  </v-flex>
                </v-layout>

                <v-layout align-center>
                  <v-flex grow>
                    <v-textarea
                      class="mt-3"
                      v-model.trim="form.basicDetails.longDescription"
                      label="Long Description - (optional)"
                    ></v-textarea>
                  </v-flex>
                  <v-flex shrink>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon color="grey lighten-1">help_outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Describe what the experiment is all about.</span>
                    </v-tooltip>
                  </v-flex>
                </v-layout>

                <v-layout align-center>
                  <v-flex grow>
                    <v-select
                      class="mt-3"
                      v-model="form.basicDetails.experimentType"
                      :items="experimentTypes"
                      item-text="title"
                      item-value="id"
                      label="Experiment Type"
                    ></v-select>
                  </v-flex>
                  <v-flex shrink style="opacity: 0;">
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon color="grey lighten-1">info</v-icon>
                        </v-btn>
                      </template>
                      <span></span>
                    </v-tooltip>
                  </v-flex>
                </v-layout>

                <v-layout align-center>
                  <v-flex grow>
                    <v-select
                      class="mt-3"
                      v-model="form.basicDetails.algorithm"
                      :items="['Random queue', 'Custom queue']"
                      label="Algorithm"
                    ></v-select>
                  </v-flex>
                  <v-flex shrink style="opacity: 0;">
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon color="grey lighten-1">info</v-icon>
                        </v-btn>
                      </template>
                      <span></span>
                    </v-tooltip>
                  </v-flex>
                </v-layout>
              </v-flex>
            </v-form>
          </v-card>
        </v-stepper-content>

        <v-stepper-content step="2">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ steps[1].title }}</h2>
            <v-checkbox
              v-model="form.settings.timer"
              color="success"
              :label="`Display timer for observer`"
            ></v-checkbox>

            <v-checkbox
              v-model="form.settings.colourVisionDeficiencies"
              color="success"
              :label="`Allow observer with colour vision deficiencies (did not pass Ishihara test)`"
            ></v-checkbox>

            <v-checkbox
              v-model="form.settings.forcedChoice"
              color="success"
              :label="`Forced choice`"
            ></v-checkbox>

            <v-layout class="mt-3">
              <v-flex xs6>
                <v-text-field
                  v-model="form.settings.bgColour"
                  label="Background colour"
                  prefix="#"
                  placeholder="808080"
                ></v-text-field>
              </v-flex>

              <!-- <v-flex shrink class="ml-2"
                :style="'border-radius: 2px; width: 40px; height: 40px; background:' + form.settings.bgColour"
              ></v-flex> -->
            </v-layout>

            <v-layout class="mt-3">
              <v-flex xs6>
                <v-text-field
                  v-model="form.settings.stimuliSpacing"
                  label="Stimuli separation distance"
                  suffix="px"
                  type="text"
                >
                  <template v-slot:append-outer>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-icon v-on="on">help_outline</v-icon>
                      </template>
                      <span>Distance between stimuli images, in pixels.</span>
                    </v-tooltip>
                  </template>
                </v-text-field>
              </v-flex>
            </v-layout>

          </v-card>
        </v-stepper-content>

        <v-stepper-content step="3">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2>{{ steps[2].title }}</h2>
            <p class="body-1">
              Add instructions and image sets in the order they should appear in your experiment.
            </p>
            <order/>
          </v-card>
        </v-stepper-content>

        <v-stepper-content step="4">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ steps[3].title }}</h2>
            <observerInputs/>
          </v-card>
        </v-stepper-content>

        <v-stepper-content step="5">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ steps[4].title }}</h2>

            <!-- <div v-for="detail in form.basicDetails">
              {{ detail }}
            </div> -->
            <div class="mt-5">
              <h3 class="subheading mb-2">Experiment details</h3>
              <div class="mb-1">Title</div>
              <div class="mb-1">Description</div>
              <div class="mb-1">Type</div>
              <div class="mb-1">Algorithm</div>
              <code class="mb-1">v * 23 / 34 + 1</code>
            </div>

            <div class="mt-5">
              <h3 class="subheading mb-2">System details</h3>
              <div class="mb-1">Time inbetween stimuli, 0.6 milliseconds</div>
              <div class="mb-1">What</div>
              <div class="mb-1">Hello</div>
              <div class="mb-1">Hello</div>
            </div>
          </v-card>
        </v-stepper-content>
      </v-stepper-items>

      <v-container>
        <v-layout justify-space-between>
          <div>
            <v-btn v-if="currentLevel !== 1" @click="previous" flat color="secondary">
              <v-icon dark>keyboard_arrow_left</v-icon>Back
            </v-btn>
          </div>

          <div>
            <v-btn v-if="currentLevel !== steps.length" @click="next" depressed color="primary">
              Continue<v-icon>keyboard_arrow_right</v-icon>
            </v-btn>

            <template v-if="currentLevel === steps.length">
              <v-btn color="secondary" flat outline :loading="save">
                Save as hidden
              </v-btn>

              <span>OR</span>

              <v-btn @click="publish" depressed color="#78AA1C" dark :disable="publishing" :loading="publishing" large>
                <template v-if="$route.params.id"> <!-- if we're editing -->
                  Save changes
                </template>
                <template v-else>
                  Publish
                </template>
              </v-btn>
            </template>
          </div>
        </v-layout>
      </v-container>

      <!-- <v-btn flat>Cancel</v-btn> -->
    </v-stepper>
  </div>
</template>

<script>
import order from '@/components/scientist/Order'
import observerInputs from '@/components/scientist/ObserverInputs'
import EventBus from '@/eventBus'

export default {
  components: {
    order,
    observerInputs
  },

  data () {
    return {
      currentLevel: 1,

      // mode: this.$route.params.id,
      experimentTypes: [
        { id: 1, title: 'Rank order' },
        { id: 2, title: 'Paired comparison' },
        { id: 3, title: 'Category judgement' },
        { id: 4, title: 'Triplet comparison' },
        { id: 5, title: 'Artifact marking' }
      ],

      steps: [
        { title: 'Basic Details' },
        { title: 'Settings' },
        { title: 'Experiment Steps', subText: 'Instructions and Image Sets' },
        { title: 'Observer Inputs' },
        { title: 'Summary' }
        // Final Checks? and/or summary
      ],

      form: {
        basicDetails: {
          title: null,
          shortDescription: null,
          longDescription: null,
          experimentType: null,
          algorithm: null
        },

        settings: {
          timer: false,
          colourVisionDeficiencies: true,
          forcedChoice: false,
          bgColour: '808080',
          stimuliSpacing: 20
        }
      },

      publishing: false,
      save: false
    }
  },

  created () {
    if (this.$route.params.id) {
      // fake ajax request
      // this.form.basicDetails = {
      //   title: 'Red chroma evaluation expriment thing',
      //   shortDescription: '',
      //   longDescription: `A test under controlled conditions that is made to demonstrate a known truth, to examine the validity of a hypothesis, or to determine the efficacy of something previously untried.`,
      //   type: 'Rank order',
      //   algorithm: 'Random'
      // }
      console.log(this.$route.params.id)
      this.findExperiment(this.$route.params.id)

      // this.$axios.patch('/experiment/10', {
      //   title: 'new title',
      //   experiment_type: 1
      // }).then(response => {
      //   console.log(response)
      // }).catch((error) => {
      //   console.log(error)
      // })
    }
  },

  methods: {
    next () {
      if (this.currentLevel === this.steps.length) this.currentLevel = 0
      this.currentLevel++
    },

    previous () {
      if (this.currentLevel === 1) this.currentLevel = this.steps.length + 1
      this.currentLevel--
    },

    publish () {
      this.publishing = true

      this.$axios.post('/experiment/store', this.form.basicDetails).then(response => {
        EventBus.$emit('experimentCreated', 'Experiment succesfully created.')

        this.publishing = false
        this.$router.push('/scientist/experiment/experiments')
      }).catch(() => {
        this.publishing = false
      })
    },

    // async publish () {
    //   this.publishing = true

    //   const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
    //   await pause(1500)

    //   this.publishing = false

    //   this.$router.push('/scientist/experiment/experiments')
    // },

    async findExperiment (id) {
      return this.$axios.get('/experiment/' + id)
        .then(json => (this.form.basicDetails = json.data))
        .catch(err => console.warn(err))
    },

    async exists () {
      const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
      await pause(1500)
    }
  }
}
</script>

<style scoped lang="scss">
.no-transition .v-stepper__content {
  transition: none;
}
</style>
