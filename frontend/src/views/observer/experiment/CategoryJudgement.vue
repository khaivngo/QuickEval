<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: #' + experiment.background_colour">
    <v-toolbar flat height="50" color="#282828">
      <v-toolbar-items>
        <v-dialog persistent v-model="instructionDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn flat dark color="#D9D9D9" v-on="on">
              Instructions
            </v-btn>
          </template>
          <v-card style="background-color: grey; color: #fff;">
            <v-card-title class="headline">
              Instructions
            </v-card-title>

            <v-card-text v-html="instructionText"></v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="primary darken-1"
                flat="flat"
                @click="instructionDialog = false"
              >
                Close
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar-items>

      <v-toolbar-items>
        <v-btn color="#D9D9D9" flat dark left class="panning-reset">
          <span>Reset image panning</span>
        </v-btn>
      </v-toolbar-items>

      <v-spacer></v-spacer>

      <v-toolbar-items>
        <v-dialog v-model="abortDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn flat dark color="#D9D9D9" v-on="on">
              Quit Experiment
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="headline">Do you want to quit the experiment?</v-card-title>
            <v-card-text></v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="default darken-1" flat @click="abortDialog = false">Continue</v-btn>
              <v-btn color="red darken-1" flat @click="abort">Quit</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar-items>
    </v-toolbar>

    <v-layout mt-4 mb-1 ml-3 mr-3 pa-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <v-layout pa-0 ma-0 justify-center>
          <div class="pl-2 pr-2 category-select">
            <v-select
              v-model="selectedCategory"
              :items="categories"
              menu-props="auto"
              label="Select category"
              item-text="title"
              item-value="id"
              hide-details
              single-line
              class="ma-0 pt-0"
            ></v-select>
          </div>
        </v-layout>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="text-xs-center" v-if="experiment.show_original === 1">
        <h4 class="subheading font-weight-regular">Original</h4>
      </v-flex>
    </v-layout>

    <v-layout ml-3 mr-3 pa-0 style="height: 85vh;" justify-center>
      <v-flex ma-2 class="picture-container">
        <div class="panzoom">
          <img id="picture-left" class="picture" :src="leftImage"/>
        </div>
      </v-flex>

      <v-flex ma-2 class="picture-container" v-if="experiment.show_original === 1">
        <div class="panzoom">
          <img id="picture-original" class="picture" :src="originalImage"/>
        </div>
      </v-flex>

      <!-- <v-flex
        class="picture-container"
        :class="rightReproductionActive ? 'selected' : ''"
        @click="toggleSelected('right')"
        xs4 ma-2
      >
        <div class="panzoom">
          <img id="picture-right" class="picture" :src="rightImage"/>
        </div>
      </v-flex> -->
    </v-layout>

    <!-- <div style="position: fixed; bottom: 2%; left: 1.4%; right: 50.9%;">
      <v-pagination
        :length="6"
      ></v-pagination>
      <v-tabs color="rgb(195, 195, 195)" show-arrows style="border-radius: 2px;">
        <v-tabs-slider color="#000"></v-tabs-slider>
        <v-tab
          v-for="(item, i) in ['good', 'best', 'very long criteria', 'unsure', 'I\'ve seen better', 'another critera with long text', 'excellent', 'terrible', 'terrific', 'perfect']"
          :key="i"
          :href="'#tab-' + i"
        >
          <span style="color: #000;">{{ item }}</span>
        </v-tab>
      </v-tabs>
    </div> -->

    <v-btn fixed bottom right color="#D9D9D9"
      @click="next()"
      :disabled="disableNextBtn || (selectedCategory === null)"
      :loading="disableNextBtn"
    >
      <span class="ml-1">next</span>
      <v-icon>keyboard_arrow_right</v-icon>
    </v-btn>

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'

export default {
  name: 'category-experiment-view',

  components: {
    FinishedDialog
  },

  data () {
    return {
      experiment: {
        id: null,
        show_original: 1,
        stimuli_seperation_distance: 20,
        background_colour: '808080'
      },

      stimuli: [],

      index: 0,
      experimentResult: null,

      categories: [],
      selectedCategory: null,

      disableNextBtn: false,

      instructionText: 'Rate the images.',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      leftImage: '',
      rightImage: ''
    }
  },

  created () {
    this.getExperiment(this.$route.params.id).then(response => {
      this.experiment = response.data

      // checkIfExperimentTaken() -> look for completed key in experimentResults table load index from localstorage or find num rows in results table
      // -> deleteoldresults()

      /* eslint-env jquery */
      $(document).ready(function () {
        (function () {
          var $pictureContainer = $('.picture-container')

          $pictureContainer.find('.panzoom').panzoom({
            $set: $pictureContainer.find('.panzoom'),
            minScale: 1,
            maxScale: 1,
            $reset: $('.panning-reset')
          }).panzoom('zoom')
        })()
      })

      this.$axios.get(`/experiment/${this.experiment.id}/categories`).then((payload) => {
        this.categories = payload.data
      })

      this.$axios.get(`/experiment/${this.experiment.id}/start`).then((payload) => {
        if (payload) {
          this.stimuli = payload.data

          if (localStorage.getItem('index') === null) {
            localStorage.setItem('index', 0)
          }

          this.index = Number(localStorage.getItem('index'))
          this.experimentResult = Number(localStorage.getItem('experimentResult'))

          this.next()
        } else {
          alert('Something went wrong. Could not start the experiment.')
        }
      }).catch(err => {
        console.warn(err)
      })
    })
  },

  methods: {
    /**
     * Load the next image stimuli queue, or instructions.
     */
    next () {
      // Have we reached the end?
      if (this.stimuli[this.index] === undefined) {
        this.onFinish()
        return
      }

      if (this.stimuli[this.index].hasOwnProperty('picture_queue_id') && this.stimuli[this.index].picture_queue_id !== null) {
        // set original
        if (this.stimuli[this.index].hasOwnProperty('original')) {
          this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].original.path
        }

        // set left reproduction image
        this.getImage(this.stimuli[this.index].picture_id).then(image => {
          this.leftImage = this.$UPLOADS_FOLDER + image.data.path
        })

        // don't do anything unless category has been selected
        if (this.selectedCategory !== null) {
          this.disableNextBtn = true

          this.store(this.stimuli[this.index]).then(response => {
            if (response.data !== 'result_stored') {
              alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
            }

            this.disableNextBtn = false
            this.selectedCategory = null
            this.index += 1
            localStorage.setItem('index', this.index)

            // Have we reached the end?
            if (this.stimuli[this.index] === undefined) {
              this.onFinish()
            }
          }).catch(() => {
            this.disableNextBtn = false
            alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
          })
        }
      } else {
        this.instructionText = this.stimuli[this.index].description
        this.instructionDialog = true

        this.index += 1
        localStorage.setItem('index', this.index)

        this.next()
      }
    },

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async getImage (id) {
      return this.$axios.get(`/picture/${id}`)
    },

    async store (pictureIdLeft) {
      let data = {
        experiment_result_id: this.experimentResult,
        category_id: this.selectedCategory,
        picture_id_left: pictureIdLeft.picture_id,
        chose_none: 0
      }

      return this.$axios.post('/category-result', data)
    },

    onFinish () {
      localStorage.removeItem('index')
      localStorage.removeItem('experimentResult')
      this.finished = true
    },

    abort () {
      localStorage.removeItem('index')
      localStorage.removeItem('experimentResult')
      this.abortDialog = true
      this.$router.push('/observer')
    }
  }
}
</script>

<style scoped lang="scss">
  .qe-wrapper {
    background-color: #808080;
    min-height: 100vh;
    overflow: hidden;
    margin: 0;
    padding: 0;
  }

  .panzoom {
    max-height: 100%;
  }

  .selected {
    outline: solid 5px #282828;
  }

  .category-select {
    max-width: 250px;
    background-color: #bbb;
  }
</style>
