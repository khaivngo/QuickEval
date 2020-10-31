<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: #' + experiment.background_colour">
    <v-toolbar flat height="30" color="#282828">
      <v-toolbar-items>
        <v-dialog persistent v-model="instructionDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn text dark color="#D9D9D9" v-on="on">
              Instructions
            </v-btn>
          </template>
          <v-card style="background-color: grey; color: #fff;">
            <v-card-title class="headline">
              Instructions
            </v-card-title>

            <v-card-text v-html="instructionText" style="color: #fff;"></v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="primary darken-1"
                text
                @click="instructionDialog = false"
              >
                Close
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar-items>

      <v-toolbar-items>
        <v-btn color="#D9D9D9" text dark left class="panning-reset">
          <span>Reset image panning</span>
        </v-btn>
      </v-toolbar-items>

      <v-spacer></v-spacer>

      <v-toolbar-items v-if="experiment.show_progress === 1">
        <h4 class="pt-1 mr-4" style="color: #BDBDBD;">
          {{ index2 / 3 }}/{{ (stimuli.length + stimuliIndex) / 3 }}
        </h4>
      </v-toolbar-items>

      <v-toolbar-items>
        <v-dialog v-model="abortDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn text dark color="#D9D9D9" v-on="on">
              Quit
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="headline">Do you want to quit the experiment?</v-card-title>
            <v-card-text></v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="default darken-1" text @click="abortDialog = false">Continue</v-btn>
              <v-btn color="red darken-1" text @click="abort">Quit</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar-items>
    </v-toolbar>

    <v-layout mt-4 mb-1 ml-3 mr-3 pa-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 class="text-center" v-if="experiment.show_original === 1 && originalImage">
        <h4 class="subheading font-weight-regular">
          Original
        </h4>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <v-layout pa-0 ma-0 justify-center>
          <div class="pl-2 pr-2 category-select">
            <v-select
              v-model="selectedCategoryLeft"
              :items="categories"
              :disabled="disableNextBtn"
              menu-props="auto"
              label="Select category"
              item-text="title"
              item-value="id"
              hide-details
              single-line
              class="ma-0 pt-0"
              color="#808080"
            ></v-select>
          </div>
        </v-layout>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <v-layout pa-0 ma-0 justify-center>
          <div class="pl-2 pr-2 category-select">
            <v-select
              v-model="selectedCategoryMiddle"
              :items="categories"
              :disabled="disableNextBtn"
              menu-props="auto"
              label="Select category"
              item-text="title"
              item-value="id"
              hide-details
              single-line
              class="ma-0 pt-0"
              color="#808080"
            ></v-select>
          </div>
        </v-layout>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <v-layout pa-0 ma-0 justify-center>
          <div class="pl-2 pr-2 category-select">
            <v-select
              v-model="selectedCategoryRight"
              :items="categories"
              :disabled="disableNextBtn"
              menu-props="auto"
              label="Select category"
              item-text="title"
              item-value="id"
              hide-details
              single-line
              class="ma-0 pt-0"
              color="#808080"
            ></v-select>
          </div>
        </v-layout>
      </v-flex>
    </v-layout>

    <v-layout ml-3 mr-3 pa-0 style="height: 85vh;" justify-center>
      <v-flex
        mt-2 mb-2
        class="picture-container"
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
      >
        <div class="panzoom">
          <img id="picture-original" class="picture" :src="originalImage"/>
        </div>
      </v-flex>

      <v-flex mt-2 mb-2 class="picture-container" :style="'margin-right:' + experiment.stimuli_spacing + 'px'">
        <div class="panzoom">
          <img id="picture-left" class="picture" :class="isLoadLeft === false ? 'hide' : ''" :src="imageLeft"/>
        </div>
      </v-flex>

      <v-flex mt-2 mb-2 class="picture-container" :style="'margin-right:' + experiment.stimuli_spacing + 'px'">
        <div class="panzoom">
          <img id="picture-right" class="picture" :class="isLoadMiddle === false ? 'hide' : ''" :src="imageMiddle"/>
        </div>
      </v-flex>

      <v-flex mt-2 mb-2 class="picture-container">
        <div class="panzoom">
          <img id="picture-right" class="picture" :class="isLoadRight === false ? 'hide' : ''" :src="imageRight"/>
        </div>
      </v-flex>
    </v-layout>

    <v-btn
      fixed bottom right
      color="#D9D9D9"
      @click="next()"
      :disabled="disableNextBtn || (selectedCategoryLeft === null || selectedCategoryMiddle === null || selectedCategoryRight === null)"
      :loading="disableNextBtn"
    >
      <span class="ml-1">next</span>
      <v-icon>mdi-chevron-right</v-icon>
    </v-btn>

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'
import { datetimeToSeconds } from '@/functions/datetimeToSeconds.js'

export default {
  name: 'triplet-experiment-view',

  components: {
    FinishedDialog
  },

  data () {
    return {
      experiment: {
        id: null,
        show_original: 1,
        stimuli_spacing: 15,
        background_colour: '808080',
        delay: 200
      },

      selectedCategoryLeft: null,
      selectedCategoryMiddle: null,
      selectedCategoryRight: null,

      isLoadRight: false,
      isLoadMiddle: false,
      isLoadLeft: false,

      stimuli: [],

      index: 0,
      index2: 0,
      stimuliIndex: 0,
      experimentResult: null,

      categories: [],

      disableNextBtn: false,

      instructionText: 'Rate the images.',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      imageLeft: '',
      imageMiddle: '',
      imageRight: '',

      startTime: null
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

          // count how many instructions we have
          let count = payload.data.filter((obj) => obj.instruction_id).length
          let count2 = count * 2 // 3
          // let min = payload.data - count
          this.stimuliIndex = count2

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

      window.addEventListener('keydown', (e) => {
        // esc
        if (e.keyCode === 27) {
          this.abort()
        }
        // arrow right
        if (e.keyCode === 39) {
          if (this.selectedCategoryLeft !== null && this.selectedCategoryMiddle !== null && this.selectedCategoryRight !== null) {
            this.next()
          }
        }
      })
    })
  },

  methods: {
    datetimeToSeconds: datetimeToSeconds,

    /**
     * Load the next image queue stimuli, or instructions.
     */
    next () {
      // Have we reached the end?
      if (this.stimuli[this.index + 2] === undefined) {
        this.onFinish()
        return
      }

      // is the next stimuli of type image?
      if (this.stimuli[this.index].hasOwnProperty('picture_queue_id') && this.stimuli[this.index].picture_queue_id !== null) {
        // set original
        if (
          this.stimuli[this.index].hasOwnProperty('original') &&
          this.stimuli[this.index].hasOwnProperty('original') !== null &&
          this.stimuli[this.index].original &&
          this.stimuli[this.index].original.path
        ) {
          this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].original.path
        } else {
          this.originalImage = ''
        }

        const imgLeft = new Image()
        imgLeft.src = this.$UPLOADS_FOLDER + this.stimuli[this.index].path
        imgLeft.onload = () => {
          this.isLoadLeft = false
          this.imageLeft = imgLeft.src
          window.setTimeout(() => {
            this.isLoadLeft = true
            // starts or overrides existing timer
            this.startTime = new Date()
            this.disableNextBtn = false
          }, this.experiment.delay)
        }

        const imgMiddle = new Image()
        imgMiddle.src = this.$UPLOADS_FOLDER + this.stimuli[this.index + 1].path
        imgMiddle.onload = () => {
          this.isLoadMiddle = false
          this.imageMiddle = imgMiddle.src
          window.setTimeout(() => {
            this.isLoadMiddle = true
            // starts or overrides existing timer
            this.startTime = new Date()
            this.disableNextBtn = false
          }, this.experiment.delay)
        }

        const imgRight = new Image()
        imgRight.src = this.$UPLOADS_FOLDER + this.stimuli[this.index + 1].path
        imgRight.onload = () => {
          this.isLoadRight = false
          this.imageRight = imgRight.src
          window.setTimeout(() => {
            this.isLoadRight = true
            // starts or overrides existing timer
            this.startTime = new Date()
            this.disableNextBtn = false
          }, this.experiment.delay)
        }

        /* don't do anything unless all categories has been selected */
        if (this.selectedCategoryLeft !== null && this.selectedCategoryMiddle !== null && this.selectedCategoryRight !== null) {
          this.disableNextBtn = true

          // record the current time
          let endTime = new Date()
          // get the number of seconds between endTime and startTime
          let seconds = datetimeToSeconds(this.startTime, endTime)

          this.store(this.stimuli[this.index], this.stimuli[this.index + 1], this.stimuli[this.index + 2], seconds).then(response => {
            if (response.data !== 'result_stored') {
              alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
            }

            this.selectedCategoryLeft = null
            this.selectedCategoryMiddle = null
            this.selectedCategoryRight = null
            this.index += 3
            this.index2 += 3
            localStorage.setItem('index', this.index)

            // Have we reached the end?
            if (this.stimuli[this.index + 2] === undefined) {
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
        this.index2 += 3
        localStorage.setItem('index', this.index)

        this.next()
      }
    },

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async store (pictureIdLeft, pictureIdMiddle, pictureIdRight, clientSideTimer) {
      /* eslint-disable */
      let data = {
        experiment_result_id: this.experimentResult,
        category_id_left:     this.selectedCategoryLeft,
        category_id_middle:   this.selectedCategoryMiddle,
        category_id_right:    this.selectedCategoryRight,
        picture_id_left:      pictureIdLeft.picture_id,
        picture_id_middle:    pictureIdMiddle.picture_id,
        picture_id_right:     pictureIdRight.picture_id,
        client_side_timer:    clientSideTimer,
        chose_none: 0
      }
      /* eslint-enable */

      return this.$axios.post('/triplet-result', data)
    },

    onFinish () {
      this.originalImage = ''
      this.imageLeft = ''
      this.imageMiddle = ''
      this.imageRight = ''

      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

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

  .hide {
    opacity: 0;
  }

  /* override default v-select dropdown colours */
  .theme--light.v-application {
    background-color: #bbb;
    // color: #fff;
  }
  .theme--light.v-list {
    background: #bbb;
    // color: #fff;
  }
  .theme--light.v-list.v-list-item__content.v-list-item__title {
    color: #fff;
  }
  // .theme--light.v-list-item:hover:before {
  //   opacity: 0.4;
  // }
</style>
