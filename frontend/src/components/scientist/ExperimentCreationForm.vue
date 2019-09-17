<template>
  <div>
    <v-layout mb-5>
      <h2 class="display-1">
        Create Experiment
      </h2>
    </v-layout>

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
                      v-model.trim="form.title"
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
                      v-model.trim="form.shortDescription"
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
                      v-model.trim="form.longDescription"
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
                      v-model="form.experimentType"
                      :items="experimentTypes"
                      item-text="name"
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
                      v-model="form.algorithm"
                      :items="['Random Queue']"
                      label="Algorithm"
                      :disabled="true"
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
              v-model="form.timer"
              color="success"
              :label="`Display timer for observer`"
            ></v-checkbox>

            <v-checkbox
              v-model="form.cvd"
              color="success"
              :label="`Allow observers with colour vision deficiencies (did not pass Ishihara test)`"
            ></v-checkbox>

            <v-layout align-center>
              <v-flex shrink>
                <v-checkbox
                  v-model="form.showOriginal"
                  color="success"
                  :label="`Display original image`"
                ></v-checkbox>
              </v-flex>
              <v-flex shrink ml-5>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">help_outline</v-icon>
                    </v-btn>
                  </template>
                  <span>
                    Display the original image of the image set.
                  </span>
                </v-tooltip>
              </v-flex>
            </v-layout>

            <!-- <v-checkbox
              v-model="form.forcedChoice"
              color="success"
              :label="`Forced choice`"
            ></v-checkbox> -->

            <v-layout align-center>
              <v-flex shrink>
                <v-checkbox
                  v-model="form.samePairTwice"
                  color="success"
                  :label="`Same pair twice (flipped)`"
                ></v-checkbox>
              </v-flex>
              <v-flex shrink ml-5>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">help_outline</v-icon>
                    </v-btn>
                  </template>
                  <span>
                    Each pair of images will have their positin flipped in the queue. Leading
                    to double the comparisons for the observer.
                  </span>
                </v-tooltip>
              </v-flex>
            </v-layout>

            <v-layout class="mt-3">
              <v-flex xs6>
                <v-text-field
                  v-model="form.bgColour"
                  label="Background colour"
                  prefix="#"
                  placeholder="808080"
                ></v-text-field>
              </v-flex>

              <!-- <v-flex shrink class="ml-2"
                :style="'border-radius: 2px; width: 40px; height: 40px; background:' + form.bgColour"
              ></v-flex> -->
            </v-layout>

            <v-layout class="mt-3">
              <v-flex xs6>
                <v-text-field
                  v-model="form.stimuliSpacing"
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
            <Sequence @added="onSequence"/>
          </v-card>
        </v-stepper-content>

        <v-stepper-content step="4">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ steps[3].title }}</h2>
            <ObserverMetas @added="onObserverMeta"/>
          </v-card>
        </v-stepper-content>

        <!-- <v-stepper-content step="6" v-if="form.experimentType === 2">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2>{{ steps[2].title }}</h2>
            <p class="body-1">
              Add instructions and image sets in the order they should appear in your experiment.
            </p>
            <Sequence/>
          </v-card>
        </v-stepper-content> -->

        <v-stepper-content step="5">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ steps[4].title }}</h2>

            <!-- <div v-for="detail in form.basicDetails">
              {{ detail }}
            </div> -->
            <div class="mt-5">
              <h3 class="title mb-2">Experiment details</h3>
              <div class="mb-1">Title</div>
              <div class="mb-1">Description</div>
              <div class="mb-1">Type</div>
              <div class="mb-1">Algorithm</div>
              <code class="mb-1">v * 23 / 34 + 1</code>
            </div>

            <div class="mt-5">
              <h3 class="title mb-2">System details</h3>
              <div class="mb-1">Delay of 0.6 milliseconds inbetween stimuli</div>
              <div class="mb-1">Algorithm for making random queue. 2 * 2</div>
              <!-- <div class="mb-1">What</div>
              <div class="mb-1">Hello</div>
              <div class="mb-1">Hello</div> -->
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
              <v-btn
                @click="mode === 'new' ? store('hidden') : update()"
                color="secondary" flat outline
                :disable="loaders.saving"
                :loading="loaders.saving"
              >
                Finish later
              </v-btn>

              <span>OR</span>

              <v-btn
                @click="mode === 'new' ? store('public') : update()"
                color="#78AA1C" depressed dark large
                :disable="loaders.storing"
                :loading="loaders.storing"
              >
                <template v-if="mode === 'new'">
                  Publish
                </template>
                <template v-else>
                  Save changes
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
import Sequence from '@/components/scientist/Sequence'
import ObserverMetas from '@/components/scientist/ObserverMetas'
import EventBus from '@/eventBus'

export default {
  components: {
    Sequence,
    ObserverMetas
  },

  data () {
    return {
      currentLevel: 1,

      experimentTypes: [],

      steps: [
        { title: 'Basic Details' },
        { title: 'Settings' },
        { title: 'Experiment Steps', subText: 'Instructions and Image Sets' },
        { title: 'Observer Inputs' },
        { title: 'Summary' } // final checks?
        // { title: 'Categories' }
      ],

      form: {
        title: null,
        shortDescription: null,
        longDescription: null,
        experimentType: null,
        algorithm: 'Random Queue',
        timer: null,
        cvd: null,
        forcedChoice: false,
        samePairTwice: false,
        bgColour: '808080',
        stimuliSpacing: 20,
        showOriginal: false,
        isPublic: 0,
        sequences: [],
        observerMetas: []
      },

      loaders: {
        storing: false,
        saving: false
      }
    }
  },

  computed: {
    mode () {
      return (this.$route.params.id !== undefined) ? 'edit' : 'new'
    }
  },

  created () {
    if (this.$route.params.id) {
      this.findExperiment(this.$route.params.id)
    }

    this.$axios.get('/experiment-types')
      .then(json => {
        this.experimentTypes = json.data
      })
  },

  methods: {
    next () {
      if (this.currentLevel === this.steps.length) {
        this.currentLevel = 0
      }
      this.currentLevel++
    },

    previous () {
      if (this.currentLevel === 1) {
        this.currentLevel = this.steps.length + 1
      }
      this.currentLevel--
    },

    onSequence (values) {
      this.form.sequences = values
    },

    onObserverMeta (values) {
      this.form.observerMetas = values
    },

    store (type) {
      // determine which button as been click in order to show a loading spinner in that button
      this.loaders.storing = (type === 'public')
      this.loaders.saving = (type === 'hidden')

      // convert values from boolean to integer before saving
      this.form.showOriginal = (this.form.showOriginal === false) ? 0 : 1
      this.form.isPublic = (type === 'hidden') ? 0 : 1

      this.$axios.post('/experiment/store', this.form).then(response => {
        // if (response.data === 'experiment_stored') {
        EventBus.$emit('success', 'Experiment succesfully created.')

        // EMPTY FORM: {}

        this.loaders.storing = false
        this.loaders.saving = false

        this.$router.push('/scientist/experiment/experiments')
        // }
      }).catch(() => {
        this.loaders.storing = false
        this.loaders.saving = false
      })
    },

    update () {
      // remember: leave is_public alone
      this.$axios.patch('/experiment/' + this.$route.params.id).then(response => {
        // redirect
      }).catch((error) => {
        console.log(error)
      })
    },

    findExperiment (id) {
      this.$axios.get('/experiment/' + id)
        .then(json => {
          console.log(json.data)
          this.form.title = json.data.title
          this.form.shortDescription = json.data.short_description
          this.form.longDescription = json.data.long_description
          this.form.experimentType = json.data.experiment_type
          this.form.timer = json.data.title
          this.form.cvd = json.data.allow_colour_blind
          this.form.bgColour = json.data.background_colour
        })
        .catch(err => console.warn(err))
    }
  }
}
</script>

<style scoped lang="scss">
.no-transition .v-stepper__content {
  transition: none;
}
</style>
