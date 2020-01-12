<template>
  <div style="width: 900px;">
    <v-layout mb-5>
      <h2 class="display-1">
        Create Experiment
      </h2>
    </v-layout>

    <v-stepper v-model="currentLevel" alt-labels non-linear>
      <Back>Back to your experiments</Back>

      <v-stepper-header class="elevation-0">
        <template v-for="(item, i) in steps">
          <v-stepper-step :step="item.id" :key="i" editable>
            {{ item.title }}
          </v-stepper-step>

          <v-divider v-if="i !== steps.length - 1" :key="i + 100"/>
        </template>
      </v-stepper-header>

      <v-stepper-items class="no-transition">
        <v-stepper-content :step="showBasicDetails.id">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ steps[0].title }}</h2>

            <v-layout align-center pr-5>
              <v-flex>
                <v-select
                  class="mt-4"
                  v-model="form.experimentType"
                  :disabled="mode === 'edit'"
                  :items="experimentTypes"
                  item-text="name"
                  item-value="id"
                  label="Experiment Type"
                ></v-select>
              </v-flex>
            </v-layout>

            <v-layout align-center pr-5>
              <v-flex>
                <v-text-field
                  class="mt-4"
                  v-model.trim="form.title"
                  label="Experiment Name"
                ></v-text-field>
              </v-flex>
            </v-layout>

            <!-- <v-layout align-center>
              <v-flex grow>
                <v-text-field
                  class="mt-4"
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
                  <span class="pl-2 pr-2 pt-2 pb-2 body-1">
                    Describe the essence of the experiment.
                  </span>
                </v-tooltip>
              </v-flex>
            </v-layout> -->

            <v-layout align-center>
              <v-flex grow>
                <v-textarea
                  class="mt-4"
                  v-model.trim="form.longDescription"
                  label="Long Description - (optional)"
                >
                  <template v-slot:label>
                    Description -<span class="caption"> (optional)</span>
                  </template>
                </v-textarea>
              </v-flex>
              <v-flex shrink>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">help_outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 body-1">
                    Describe what the experiment is all about.<br>
                    This description will be available to the observers.
                  </div>
                </v-tooltip>
              </v-flex>
            </v-layout>

            <v-layout align-center pr-5>
              <v-flex>
                <v-select
                  class="mt-4"
                  v-model="form.algorithm"
                  :items="[
                    { id: 1, text: 'Order of images within image sets' },
                    {
                      id: 2,
                      text: 'Order of images within image sets AND order of the image sets',
                      caption: 'Note: sets will only be randomized inbetween instructions so the relationships between sets and instructions are maintained.'
                    }
                  ]"
                  item-text="text"
                  item-value="id"
                  :menu-props="{maxHeight:'auto'}"
                  label="Randomization Algorithm"
                >
                  <template slot="item" slot-scope="{ item }">
                    <v-list-tile-content>
                      <v-list-tile-title v-html="item.text"></v-list-tile-title>
                      <v-list-tile-sub-title v-html="item.caption"></v-list-tile-sub-title>
                    </v-list-tile-content>
                  </template>
                </v-select>
              </v-flex>
            </v-layout>

            <v-layout align-center v-if="form.experimentType === 1">
              <v-flex shrink>
                <v-checkbox
                  v-model="form.samePairTwice"
                  color="success"
                  :label="`Same pair twice (flipped)`"
                ></v-checkbox>
              </v-flex>
              <v-flex shrink pb-1>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">help_outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 body-1">
                    Each pair of images will have their position flipped in the queue.<br>
                    Leading to double the comparisons for the observer.
                  </div>
                </v-tooltip>
              </v-flex>
            </v-layout>

            <v-layout align-center>
              <v-flex shrink>
                <v-checkbox
                  v-model="form.showOriginal"
                  color="success"
                  :label="`Display original image`"
                ></v-checkbox>
              </v-flex>
              <v-flex shrink pb-1>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">help_outline</v-icon>
                    </v-btn>
                  </template>
                  <span class="pl-2 pr-2 body-1">
                    Display the original image of the image set alongside the reproductions.
                    As a reference for the observer.
                  </span>
                </v-tooltip>
              </v-flex>
            </v-layout>

          </v-card>
        </v-stepper-content>

        <v-stepper-content :step="showLayout.id">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-4">{{ showLayout.title }}</h2>
            <!-- <v-checkbox
              v-model="form.timer"
              color="success"
              :label="`Display timer for observer`"
            ></v-checkbox>

            <v-checkbox
              v-model="form.cvd"
              color="success"
              :label="`Allow observers with colour vision deficiencies (did not pass Ishihara test)`"
            ></v-checkbox> -->

            <!-- <v-checkbox
              v-model="form.forcedChoice"
              color="success"
              :label="`Forced choice`"
            ></v-checkbox> -->

            <v-layout class="mt-5" align-center>
              <v-flex xs3>
                <v-text-field
                  v-model="form.bgColour"
                  label="Background colour"
                  prefix="#"
                  placeholder="808080"
                ></v-text-field>
              </v-flex>

              <v-flex
                xs1
                shrink
                class="ml-2"
                :style="'border-radius: 2px; height: 30px; width: 30px; background: #' + form.bgColour"
              ></v-flex>
            </v-layout>

            <v-layout mt-5>
              <v-flex xs4>
                <v-text-field
                  v-model="form.delay"
                  label="Delay between stimuli (gray screen)"
                  suffix="milliseconds"
                  placeholder="200"
                  style="width: 200px;"
                >
                  <!-- <template v-slot:prepend-outer>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-icon v-on="on">help_outline</v-icon>
                      </template>
                      <span class="pl-2 pr-2 body-1">Distance between stimuli images, in pixels.</span>
                    </v-tooltip>
                    <span>milliseconds</span>
                  </template> -->
                </v-text-field>
              </v-flex>
            </v-layout>

            <v-layout class="mt-5">
              <v-flex xs3>
                <v-text-field
                  v-model="form.stimuliSpacing"
                  label="Stimuli separation distance"
                  suffix="pixels"
                  type="text"
                  v-if="form.experimentType !== 3"
                >
                  <template v-slot:append-outer>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-icon v-on="on">help_outline</v-icon>
                      </template>
                      <span class="pl-2 pr-2 body-1">Spacing in pixels between stimuli images.</span>
                    </v-tooltip>
                  </template>
                </v-text-field>
              </v-flex>

              <!-- <v-layout align-center ml-3>
                <div style="border-radius: 2px; padding: 5px; background-color: #ddd;">image 1</div>
                <div style="border-radius: 2px; height: 30px; width: 40px"></div>
                <div style="border-radius: 2px; padding: 5px; background-color: #ddd;">image 2</div>
              </v-layout> -->
            </v-layout>
          </v-card>
        </v-stepper-content>

        <v-stepper-content :step="showExperimentSteps.id">
          <v-card class="mb-5 pa-5" flat>
            <h2 class="mb-1 text-xs-center">{{ showExperimentSteps.title }}</h2>
            <p class="body-1 text-xs-center">
              Add instructions and image sets in the order they should appear in your experiment.
            </p>
            <Sequence
              :sequences="experiment.sequences"
              @added="onSequence"
            />
          </v-card>
        </v-stepper-content>

        <v-stepper-content :step="showCategories.id" v-if="showCategories">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-1">{{ showCategories.title }}</h2>
            <p class="body-1">
              Add the categories the observer use to rate the images.
            </p>
            <Categories
              :categories="experiment.categories"
              @added="onCategory"
            />
          </v-card>
        </v-stepper-content>

        <v-stepper-content :step="showObserverInputs.id">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <h2 class="mb-1">{{ showObserverInputs.title }}</h2>
            <p class="body-1">
              Add input fields to collect observer demographics.
            </p>
            <ObserverMetas
              :metas="experiment.metas"
              @added="onObserverMeta"
            />
          </v-card>
        </v-stepper-content>

        <v-stepper-content :step="showFinish.id">
          <v-card class="mb-5 pa-5 text-xs-center" flat>
            <!-- <h2 class="mb-4">{{ showFinish.title }}</h2> -->

            <!-- <div v-for="detail in form.basicDetails">
              {{ detail }}
            </div> -->
            <!-- <div class="mt-5">
              <h3 class="title mb-2">Experiment details</h3>
              <div class="mb-1">Title</div>
              <div class="mb-1">Description</div>
              <div class="mb-1">Type</div>
              <div class="mb-1">Algorithm</div>
              <code class="mb-1">v * 23 / 34 + 1</code>
            </div> -->

            <!-- <div class="mt-5">
              <h3 class="title mb-2">System details</h3>
              <div class="mb-1">Delay of 0.6 milliseconds inbetween stimuli</div>
              <div class="mb-1">Algorithm for making random queue. 2 * 2</div>
            </div> -->
          </v-card>
        </v-stepper-content>
      </v-stepper-items>

      <v-layout justify-end v-if="currentLevel === steps.length && mode === 'edit'">
        <v-flex xs6 class="caption">
          <!-- Note: Saving changes will create a new experiment AND keep the original experiment.
          This is in order to keep the version history and clarify that possible collected observer data is
          collected under different versions of the experiment.
          Delete the old expriment from the experiments list later if you don't need it. -->
          Note: The original version will be kept untouched and a new version will be created with a "version 2"-tag.
          Delete the old version from the experiments list later if you don't need it.
        </v-flex>
      </v-layout>

      <!-- footer stepper actions -->
      <v-container>
        <v-layout justify-space-between>
          <div>
            <v-btn v-if="currentLevel !== 1" @click="previous" flat color="secondary">
              <v-icon dark>keyboard_arrow_left</v-icon>Back
            </v-btn>
          </div>

          <div>
            <v-btn v-if="currentLevel !== steps.length" @click="next" depressed color="primary">
              Next <v-icon>keyboard_arrow_right</v-icon>
            </v-btn>

            <template v-if="currentLevel === steps.length">
              <v-tooltip left>
                <template v-slot:activator="{ on }">
                  <v-btn
                    v-on="on"
                    v-if="mode === 'new'"
                    @click="mode === 'new' ? store('hidden') : update()"
                    color="secondary" flat outline
                    :disable="loaders.saving"
                    :loading="loaders.saving"
                  >
                    Save as hidden
                  </v-btn>
                </template>
                <div class="pa-1 body-1">
                  Experiment will only be visible to you.<br>You can publish later when you're ready.
                </div>
              </v-tooltip>

              <span v-if="mode === 'new'">OR</span>

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
    </v-stepper>
  </div>
</template>

<script>
import Sequence from '@/components/scientist/Sequence'
import ObserverMetas from '@/components/scientist/ObserverMetas'
import Categories from '@/components/scientist/Categories'
import EventBus from '@/eventBus'
import Back from '@/components/Back'
import { removeArrayItem } from '@/helpers.js'

export default {
  components: {
    Sequence,
    ObserverMetas,
    Categories,
    Back
  },

  data () {
    return {
      currentLevel: 1,

      experimentTypes: [],

      steps: [
        { id: 1, title: 'Basic Details' },
        { id: 2, title: 'Experiment Steps', subText: 'Instructions and Image Sets' },
        { id: 3, title: 'Layout' },
        { id: 4, title: 'Observer Inputs' },
        { id: 5, title: 'Final Checks' }
      ],

      experiment: {
        sequences: [],
        metas: [],
        categories: []
      },

      form: {
        title: null,
        shortDescription: null,
        longDescription: null,
        experimentType: null,
        algorithm: 1,
        timer: null,
        cvd: null,
        forcedChoice: false,
        samePairTwice: false,
        bgColour: '808080',
        delay: 200,
        stimuliSpacing: 15,
        showOriginal: false,
        isPublic: 0,
        sequences: [],
        observerMetas: [],
        categories: []
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
    },

    experimentType () {
      return this.form.experimentType
    },

    showCategories () {
      return this.steps.find(step => step.title === 'Categories')
    },

    showBasicDetails () {
      return this.steps.find(step => step.title === 'Basic Details')
    },

    showLayout () {
      return this.steps.find(step => step.title === 'Layout')
    },

    showExperimentSteps () {
      return this.steps.find(step => step.title === 'Experiment Steps')
    },

    showObserverInputs () {
      return this.steps.find(step => step.title === 'Observer Inputs')
    },

    showFinish () {
      return this.steps.find(step => step.title === 'Final Checks')
    }
  },

  watch: {
    /**
     * Some experiment types have extra steps.
     *
     * currentLevel is linked with the ID of the object, so we have to
     * increment/decrement the IDs for the currentLevel to be correct.
     */
    experimentType (newVal, oldVal) {
      // if rating -> non rating
      if ((oldVal === null || oldVal === 3 || oldVal === 5) && (newVal === 1 || newVal === 2 || newVal === 4)) {
        // if categories, remove
        if (this.showCategories) {
          --this.steps[4].id
          --this.steps[5].id
          removeArrayItem(this.steps, function (n) {
            return n.title === 'Categories'
          })
        }
      // if non rating -> rating
      } else if ((oldVal === null || oldVal === 1 || oldVal === 2 || oldVal === 4) && (newVal === 3 || newVal === 5)) {
        // if no categories, add
        ++this.steps[3].id // this.steps.find(step => step.title === 'Categories').id = 4
        ++this.steps[4].id
        this.steps.splice(3, 0, { id: 4, title: 'Categories' })
      }
    }
  },

  created () {
    if (this.$route.params.id) {
      this.findExperiment(this.$route.params.id)
    }

    this.$axios.get('/experiment-types').then(json => {
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

    onCategory (values) {
      this.form.categories = values
    },

    store (type) {
      // determine which button has been clicked in order to show a loading spinner in that button
      this.loaders.storing = (type === 'public')
      this.loaders.saving = (type === 'hidden')

      // convert values from boolean to integer before saving
      this.form.showOriginal = (this.form.showOriginal === false) ? 0 : 1
      this.form.isPublic = (type === 'hidden') ? 0 : 1

      this.$axios.post('/experiment/store', this.form).then(response => {
        // if (response.data === 'experiment_stored') {
        EventBus.$emit('success', 'Experiment successfully created.')

        // EMPTY FORM: {}

        this.loaders.storing = false
        this.loaders.saving = false

        this.$router.push('/scientist/experiments')
        // }
      }).catch(() => {
        this.loaders.storing = false
        this.loaders.saving = false
      })
    },

    update () {
      this.loaders.storing = true
      this.loaders.saving = true

      // convert values from boolean to integer before saving
      this.form.showOriginal = (this.form.showOriginal === false) ? 0 : 1
      // this.form.isPublic = (type === 'hidden') ? 0 : 1
      this.form.isPublic = 1

      this.$axios.post(`/experiment/${this.$route.params.id}/update`, this.form).then(response => {
        EventBus.$emit('success', 'Experiment successfully updated.')

        // EMPTY FORM: {}
        console.log(response)

        this.loaders.storing = false
        this.loaders.saving = false

        this.$router.push('/scientist/experiments')
      }).catch((error) => {
        console.log(error)
        EventBus.$emit('error', error.data)

        this.loaders.storing = false
        this.loaders.saving = false
      })
    },

    findExperiment (id) {
      this.$axios.get(`/experiment/${id}/owner`)
        .then(response => {
          this.experiment = response.data

          this.form.title            = response.data.title
          this.form.shortDescription = response.data.short_description
          this.form.longDescription  = response.data.long_description
          this.form.experimentType   = response.data.experiment_type_id
          this.form.timer            = response.data.title
          this.form.cvd              = response.data.allow_colour_blind
          this.form.bgColour         = response.data.background_colour || '808080'
          this.form.showOriginal     = (response.data.show_original === 1)
          this.form.samePairTwice    = response.data.same_pair
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
