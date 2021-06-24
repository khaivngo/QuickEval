<template>
  <v-container fluid class="pl-8 pr-8 pb-12 pt-6 flex-grow-1">
    <v-row class="ml-0 mr-0 mt-0 mb-12 pa-0" v-if="loaders.fetching">
      <v-col class="pl-9">
        <v-progress-linear indeterminate class="ma-0"></v-progress-linear>
      </v-col>
    </v-row>

    <v-row class="ml-0 mr-0 mb-12 mt-4 pa-0" v-if="!loaders.fetching">
      <v-col class="pl-9">
        <h2 class="text-h4" v-if="mode === 'new'">
          Create Experiment
        </h2>
        <h2 class="text-h4" v-else>
          Editing <v-icon>mdi-arrow-right</v-icon> {{ experiment.title }}
        </h2>
      </v-col>
    </v-row>

    <v-stepper v-model="currentLevel" alt-labels non-linear v-if="!loaders.fetching" class="elevation-0">
      <v-stepper-header class="elevation-0">
        <template v-for="(item, i) in steps">
          <v-stepper-step :step="item.id" :key="i" editable>
            {{ item.title }}
          </v-stepper-step>
          <!-- :rules="[() => false]" -->

          <v-divider v-if="i !== steps.length - 1" :key="i + 100"/>
        </template>
      </v-stepper-header>

      <v-stepper-items class="no-transition">
        <v-stepper-content :step="showBasicDetails.id">
          <v-card class="mb-5 pa-5" flat>
            <h2 class="mb-4">{{ steps[0].title }}</h2>

            <v-row align="center" class="pr-5">
              <v-col class="pb-0 pt-0 pr-0">
                <v-select
                  class="mt-6"
                  v-model="form.experimentType"
                  :loading="loadingExperimentTypes"
                  :disabled="mode === 'edit'"
                  :items="experimentTypes"
                  item-text="name"
                  item-value="id"
                  label="Experiment Type"
                  outlined
                  dense
                ></v-select>
              </v-col>
            </v-row>

            <v-row align="center" class="pr-5">
              <v-col class="pb-0 pt-0 pr-0">
                <v-text-field
                  class="mt-8"
                  v-model.trim="form.title"
                  label="Experiment Name"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row align="center">
              <v-col class="pb-0 pt-0 pr-0">
                <v-textarea
                  class="mt-8"
                  v-model.trim="form.longDescription"
                  label="Long Description - (optional)"
                  outlined
                >
                  <template v-slot:label>
                    Description -<span class="caption"> (optional)</span>
                  </template>
                </v-textarea>
              </v-col>
              <v-col cols="auto">
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                    Describe what the experiment is all about.<br>
                    This description will be available to the observers.
                  </div>
                </v-tooltip>
              </v-col>
            </v-row>

            <!-- <v-row align="center" class="mt-6">
              <v-col class="pb-0 pt-0 pr-0">
                <CollaboratorsAutocomplete
                  :collaborators="experiment.collaborators"
                  @added="onCollaborators"
                />
              </v-col>
              <v-col cols="auto">
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                    Share the experiment with other scientists.<br>
                    They will have full access to the experiment and results.
                  </div>
                </v-tooltip>
              </v-col>
            </v-row> -->

            <v-row align="center" class="mt-0 mt-4 pt-0">
              <v-col cols="auto" class="pt-0 pb-0 pr-0">
                <v-checkbox
                  v-model="form.showProgress"
                  color="success"
                  :label="`Display progress indicator`"
                  class="pb-0 mb-0"
                  hide-details
                ></v-checkbox>
                <p class="caption pl-8 pt-0 mt-0">
                  Display a progress indicator in the top right corner. Example: 3/34
                </p>
              </v-col>
              <!-- <v-col cols="auto" class="pa-0 mb-1">
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                    Display a progress indicator in the top right corner. Example: 3/34
                  </div>
                </v-tooltip>
              </v-col> -->
            </v-row>

            <v-row align="center" class="mt-0 pt-0">
              <v-col cols="auto" class="pt-0 pb-0 pr-0">
                <v-checkbox
                  v-model="form.ishihara"
                  color="success"
                  :label="`(Beta!) Require Ishihara test`"
                  hide-details
                ></v-checkbox>
                <p class="caption pl-8 pt-0 mt-0">
                  Run a Ishihara test at the beginning of the experiment.
                </p>
              </v-col>
              <!-- <v-col cols="auto" class="pa-0 mb-1">
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                    Run a Ishihara test at the beginning of the experiment.
                    Currently in beta.
                  </div>
                </v-tooltip>
              </v-col> -->
            </v-row>

            <v-row align="center" class="mt-0 pt-0">
              <v-col cols="auto" class="pt-0 pb-0 pr-0">
                <v-checkbox
                  v-model="form.artifact_marking"
                  color="success"
                  :label="`(Beta!) Enable artifact marking pen`"
                  hide-details
                ></v-checkbox>
                <p class="caption pl-8 pt-0 mt-0">
                  Gives the observer access to a drawing pen, allowing them to mark artifacts or interesting objects
                  in the images.
                </p>
              </v-col>
              <!-- <v-col cols="auto" class="pa-0 mb-1">
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                    Gives the observer access to a drawing pen, allowing them to<br>mark artifacts or interesting objects
                    in the images.
                  </div>
                </v-tooltip>
              </v-col> -->
            </v-row>

          </v-card>
        </v-stepper-content>

        <v-stepper-content :step="showLayout.id">
          <v-card class="mb-5 pa-5" flat>
            <h2 class="mb-4">{{ showLayout.title }}</h2>
            <!-- <v-checkbox
              v-model="form.timer"
              color="success"
              :label="`Display timer for observer`"
            ></v-checkbox> -->

            <!-- <v-checkbox
              v-model="form.cvd"
              color="success"
              :label="`Allow observers with colour vision deficiencies (did not pass Ishihara test)`"
            ></v-checkbox> -->

            <!-- <v-checkbox
              v-model="form.forcedChoice"
              color="success"
              :label="`Forced choice`"
            ></v-checkbox> -->

            <v-row class="mt-6" align="center">
              <v-col cols="4" xl="3" lg="4" md="8" sm="9">
                <v-text-field
                  v-model="form.bgColour"
                  label="Background colour"
                  prefix="#"
                  placeholder="808080"
                  outlined
                  dense
                ></v-text-field>
              </v-col>

              <v-col
                cols="auto" xl="auto" lg="auto" md="auto" sm="3"
                :style="'border-radius: 2px; height: 40px; margin-bottom: 5px; width: 50px; background: #' + form.bgColour"
              ></v-col>
            </v-row>

            <v-row class="mt-4" align="center">
              <v-col cols="4" xl="3" lg="4" md="8" sm="9">
                <v-text-field
                  v-model="form.delay"
                  label="Delay between stimuli (gray screen)"
                  outlined
                  dense
                  suffix="milliseconds"
                  placeholder="200"
                ></v-text-field>
              </v-col>
              <v-col
                cols="auto" xl="auto" lg="auto" md="auto" sm="3"
                class="pa-0 mb-1"
              >
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                    Avoid instant loading of new stimuli by adding a blank screen inbetween stimuli switching.<br>
                    Reducing memory effects from previous stimuli.
                  </div>
                </v-tooltip>
              </v-col>
            </v-row>

            <v-row class="mt-8" align="center" v-if="form.experimentType !== 3">
              <v-col cols="4" xl="3" lg="4" md="8" sm="9">
                <v-text-field
                  v-model="form.stimuliSpacing"
                  label="Stimuli separation distance"
                  outlined
                  dense
                  suffix="pixels"
                  type="text"
                ></v-text-field>
              </v-col>
              <v-col
                cols="auto" xl="auto" lg="auto" md="auto" sm="3"
                class="pa-0 mb-1"
              >
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                    Spacing in pixels between stimuli images.<br>
                    Images will not get closer than amount specified,<br>
                    but may be further apart if images are small or user's screen is large.
                  </div>
                </v-tooltip>
              </v-col>
            </v-row>
            <div class="text-caption">
              Note: Images will not get closer than amount specified, but may be further apart<br>if images
              are small or user's screen is large.
            </div>
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

            <div v-for="(error, i) in errors.errors" :key="i" style="color: red;" class="mb-3">
              {{ error[0] }}
            </div>
          </v-card>
        </v-stepper-content>
      </v-stepper-items>

      <!-- footer stepper actions -->
      <v-container class="pl-12 pr-12">
        <v-row align="center" justify="space-between" style="border-top: 1px solid #ddd;">
          <v-col cols="auto" class="pl-0">
            <v-btn v-if="currentLevel !== 1" @click="previous" text color="secondary">
              <v-icon dark>mdi-chevron-left</v-icon>Back
            </v-btn>
          </v-col>

          <v-col cols="auto" class="pr-0">
            <v-btn v-if="currentLevel !== steps.length" @click="next" color="primary">
              Next <v-icon>mdi-chevron-right</v-icon>
            </v-btn>

            <template v-if="currentLevel === steps.length">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    v-on="on"
                    v-if="mode === 'new'"
                    @click="mode === 'new' ? store('hidden') : update()"
                    color="secondary"
                    text
                    outlined
                    :disable="loaders.saving"
                    :loading="loaders.saving"
                  >
                    Save as hidden
                  </v-btn>
                </template>
                <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                  Experiment will only be visible to you.<br>You can publish later when you are ready.
                </div>
              </v-tooltip>

              <span v-if="mode === 'new'" class="mr-3 ml-3">OR</span>

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
          </v-col>
        </v-row>
      </v-container>
    </v-stepper>

    <v-dialog
      v-model="disclaimerDialog"
      persistent
      width="500"
    >
      <v-card>
        <v-card-title>
          <!-- <span class="headline">Edit title</span> -->
        </v-card-title>

        <v-card-text>
          <v-container class="pt-6" fluid style="color: #000;">
            <h4 class="text-h6 mb-4">
              <v-icon class="mr-2 mb-1">mdi-alert-outline</v-icon>
              This experiment already have observers.
            </h4>
            <p>
              The original version will be kept untouched and a new version will be created with a "version 2"-tag.
            </p>
            <p class="mt-6">
              Delete the original version from the experiments list later if you don't need it.
            </p>
          </v-container>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-btn
            color="primary"
            text
            @click="disclaimerDialog = false"
          >
            Cancel
          </v-btn>

          <v-spacer></v-spacer>

          <v-btn
            color="success"
            class="ml-3"
            @click="updateApproved()"
          >
            OK
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>

<script>
import Sequence from '@/components/scientist/Sequence'
import ObserverMetas from '@/components/scientist/ObserverMetas'
import Categories from '@/components/scientist/Categories'
// import CollaboratorsAutocomplete from '@/components/scientist/CollaboratorsAutocomplete'
import EventBus from '@/eventBus'
import { removeArrayItem } from '@/helpers.js'

import { storage, mutations } from '@/stores/store.js'

export default {
  components: {
    Sequence,
    ObserverMetas,
    Categories
    // CollaboratorsAutocomplete
  },

  data () {
    return {
      currentLevel: 1,

      storage: storage,

      experimentTypes: [],
      loadingExperimentTypes: false,

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
        categories: [],
        collaborators: []
      },

      form: {
        title: null,
        shortDescription: null,
        longDescription: null,
        experimentType: null,
        algorithm: 1,
        timer: null,
        ishihara: 0,
        artifact_marking: 0,
        cvd: null,
        forcedChoice: false,
        samePairTwice: false,
        bgColour: '808080',
        delay: 200,
        hide_image_timer: null,
        stimuliSpacing: 15,
        showOriginal: false,
        showProgress: false,
        isPublic: 0,
        sequences: [],
        observerMetas: [],
        categories: [],
        collaborators: []
      },

      loaders: {
        fetching: false,
        storing: false,
        saving: false
      },

      errors: [],

      disclaimerDialog: false
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
     * It's a bit hacky, but we have to work around limitations in the Vuetify stepper component.
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

      mutations.setExperimentType(newVal)
    }
  },

  created () {
    if (this.$route.params.id) {
      this.findExperiment(this.$route.params.id)
    }

    this.loadingExperimentTypes = true
    this.$axios.get('/experiment-types').then(json => {
      this.experimentTypes = json.data
      this.loadingExperimentTypes = false
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

    onCollaborators (values) {
      this.form.collaborators = values
    },

    store (type) {
      // determine which button has been clicked in order to show a loading spinner in that button
      this.loaders.storing = (type === 'public')
      this.loaders.saving = (type === 'hidden')

      // convert values from boolean to integer before saving
      this.form.showOriginal = (this.form.showOriginal === false) ? 0 : 1 // REMOVE!!!!!!!
      this.form.showProgress = (this.form.showProgress === false) ? 0 : 1
      this.form.isPublic = (type === 'hidden') ? 0 : 1

      this.$axios.post('/experiment/store', this.form).then(response => {
        // EMPTY FORM: {}

        this.loaders.storing = false
        this.loaders.saving = false

        EventBus.$emit('success', 'Experiment successfully created.')
        EventBus.$emit('experiment-created', response.data)

        this.$router.push(`/scientist/experiments/view/${response.data.id}`)
      }).catch((error) => {
        this.errors = error.response.data
        console.log(error.response.data)
        this.loaders.storing = false
        this.loaders.saving = false
      })
    },

    update () {
      // has anyone taken the experiment?
      // if (this.experiment.results_count > 0) {
      if (this.experiment.completed_results_count > 0) {
        this.disclaimerDialog = true
      } else {
        this.updateApproved()
      }
    },

    updateApproved () {
      this.loaders.storing = true
      this.loaders.saving = true

      this.disclaimerDialog = false

      // convert values from boolean to integer before saving
      this.form.showOriginal = (this.form.showOriginal === false) ? 0 : 1 // REMOVE!!!!!!
      this.form.showProgress = (this.form.showProgress === false) ? 0 : 1
      // this.form.isPublic = (type === 'hidden') ? 0 : 1
      this.form.isPublic = 1
      // this.form.amountObservers = this.experiment.results_count
      this.form.amountObservers = this.experiment.completed_results_count

      this.$axios.post(`/experiment/${this.$route.params.id}/update`, this.form).then(response => {
        if (this.form.amountObservers === 0) {
          EventBus.$emit('experiment-deleted', this.experiment)
        }
        EventBus.$emit('experiment-created', response.data)

        EventBus.$emit('success', 'Experiment successfully updated.')

        this.loaders.storing = false
        this.loaders.saving = false

        this.$router.push(`/scientist/experiments/view/${response.data.id}`)
      }).catch((error) => {
        this.errors = error.response.data
        EventBus.$emit('error', error.data)

        this.loaders.storing = false
        this.loaders.saving = false
      })
    },

    findExperiment (id) {
      this.loaders.fetching = true

      this.$axios.get(`/experiment/${id}/owner`)
        .then(response => {
          this.experiment = response.data

          this.form.title            = response.data.title
          this.form.shortDescription = response.data.short_description
          this.form.longDescription  = response.data.long_description
          this.form.experimentType   = response.data.experiment_type_id
          this.form.timer            = response.data.timer
          this.form.ishihara         = response.data.ishihara
          this.form.artifact_marking = response.data.artifact_marking
          this.form.cvd              = response.data.allow_colour_blind
          this.form.bgColour         = response.data.background_colour || '808080'
          this.form.showOriginal     = (response.data.show_original === 1)
          this.form.showProgress     = (response.data.show_progress === 1)
          this.form.samePairTwice    = response.data.same_pair
          this.form.algorithm        = response.data.picture_sequence_algorithm

          this.loaders.fetching = false
        })
        .catch(err => {
          console.log(err)
          this.loaders.fetching = false
        })
    }
  }
}
</script>

<style scoped lang="scss">
.no-transition .v-stepper__content {
  transition: none;
}
</style>
