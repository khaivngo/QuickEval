<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: #' + experiment.background_colour">
    <v-toolbar ref="navMain" flat height="30" color="#282828">
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
                @click="closeAndNext()"
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
          {{ index }}/{{ totalComparisons }}
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

    <v-layout ref="navMarker" pa-0 ma-0 justify-center>
      <ArtifactMarkerToolbar
        v-if="experiment.artifact_marking"
        @changed="changedDrawingTool"
        class="pt-3 pb-3"
      />
    </v-layout>

    <!-- <v-layout ref="titles" pt-4 ml-3 mr-3 pa-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 class="text-center" v-if="experiment.show_original === 1">
        <h4 class="subtitle-1 pb-0 pt-3 mb-0">
          Original
        </h4>
      </v-flex>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center></v-flex>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center></v-flex>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center></v-flex>
    </v-layout> -->

    <v-layout ref="images" fill-height ml-3 mt-0 mb-0 mr-3 pa-0 pt-0 justify-center>
      <v-flex
        mt-2 mb-2
        class="picture-container"
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
        v-if="experiment.show_original === 1"
      >
        <div class="panzoom d-flex justify-center align-center">
          <img id="picture-original" class="picture" :src="originalImage"/>
        </div>
      </v-flex>

      <v-flex mt-2 mb-2 class="picture-container" :style="'margin-right:' + experiment.stimuli_spacing + 'px'">
        <div class="panzoom d-flex justify-center align-center">
          <img
            v-if="!experiment.artifact_marking"
            id="picture-left" class="picture"
            :class="isLoadLeft === false ? 'hide' : ''"
            :src="imageLeft"
          />
          <ArtifactMarker
            v-if="experiment.artifact_marking"
            @updated="drawn"
            :imageURL="leftCanvas"
            :tool="drawingTool"
          />
        </div>
      </v-flex>

      <v-flex mt-2 mb-2 class="picture-container" :style="'margin-right:' + experiment.stimuli_spacing + 'px'">
        <div class="panzoom d-flex justify-center align-center">
          <img
            v-if="!experiment.artifact_marking"
            id="picture-middle" class="picture"
            :class="isLoadMiddle === false ? 'hide' : ''"
            :src="imageMiddle"
          />
          <ArtifactMarker
            v-if="experiment.artifact_marking"
            @updated="drawn"
            :imageURL="middleCanvas"
            :tool="drawingTool"
          />
        </div>
      </v-flex>

      <v-flex mt-2 mb-2 class="picture-container">
        <div class="panzoom d-flex justify-center align-center">
          <img
            v-if="!experiment.artifact_marking"
            id="picture-right" class="picture"
            :class="isLoadRight === false ? 'hide' : ''"
            :src="imageRight"
          />
          <ArtifactMarker
            v-if="experiment.artifact_marking"
            @updated="drawn"
            :imageURL="rightCanvas"
            :tool="drawingTool"
          />
        </div>
      </v-flex>
    </v-layout>

    <v-layout ref="navAction" class="justify-end pr-6">
      <v-flex v-if="experiment.show_original === 1 && originalImage !== ''" ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <h4 class="subtitle-1 pb-0 mb-0 text-center">
          Original
        </h4>
      </v-flex>
      <v-flex ml-2 mr-2 xs6>
        <v-layout pa-0 ma-0 justify-center>
          <div class="pl-2 pr-2 category-select">
            <v-select
              v-model="selectedCategoryLeft"
              :items="categories"
              :disabled="disableNextBtn"
              :menu-props="{ maxHeight: 400, overflowY: true }"
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
              :menu-props="{ maxHeight: 400, overflowY: true }"
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
              :menu-props="{ maxHeight: 400, overflowY: true }"
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

    <v-layout ref="navNext" class="justify-end pt-4 pr-6">
      <v-btn
        color="#D9D9D9"
        @click="next()"
        :disabled="disableNextBtn || (selectedCategoryLeft === null || selectedCategoryMiddle === null || selectedCategoryRight === null)"
        :loading="disableNextBtn"
      >
        <span class="ml-1">next</span>
        <v-icon>mdi-chevron-right</v-icon>
      </v-btn>
    </v-layout>

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'
import ArtifactMarkerToolbar from '@/components/ArtifactMarkerToolbar'
import ArtifactMarker from '@/components/ArtifactMarker'
import { datetimeToSeconds } from '@/functions/datetimeToSeconds.js'

export default {
  name: 'triplet-experiment-view',

  components: {
    FinishedDialog,
    ArtifactMarkerToolbar,
    ArtifactMarker
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

      totalComparisons: 0,

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

      typeIndex: 0,
      sequenceIndex: 0,
      imagePairIndex: 0,
      loadNextImages: true,
      firstImages: 1,

      startTime: null,

      // artifact marking
      leftCanvas: '',
      middleCanvas: '',
      rightCanvas: '',
      shapes: {},
      drawingTool: ''
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

      // if localStorage does not exists for this experiment fetch new data
      const exists = Number(localStorage.getItem(`${this.experiment.id}-stimuliQueue`))
      if (exists === null || exists === 0) {
        this.startNewExperiment()
      } else {
        this.continueExistingExperiment()
      }

      window.addEventListener('keydown', (e) => {
        if (e.keyCode === 13 || e.keyCode === 39 || e.keyCode === 32) { // enter / arrow right / space
          if (this.selectedCategoryLeft !== null && this.selectedCategoryMiddle !== null && this.selectedCategoryRight !== null) {
            this.next()
          }
        }

        // esc
        if (e.keyCode === 27) {
          this.abort()
        }
      })
    })
  },

  methods: {
    datetimeToSeconds: datetimeToSeconds,

    drawn (shapes) {
      // shapes.uuid let's us distinguish between left and right image canvas
      this.shapes[shapes.uuid] = shapes.shapes
    },

    changedDrawingTool (string) {
      this.drawingTool = string
    },

    closeAndNext () {
      this.instructionDialog = false
      // this.focusSelect()
      this.next()
    },

    continueExistingExperiment () {
      this.getProgress()
      this.countTotalComparisons()
      this.next()
      this.calculateLayout()
    },

    startNewExperiment () {
      this.$axios.get(`/experiment/${this.experiment.id}/start`).then((payload) => {
        if (payload) {
          this.stimuli = Object.values(payload.data)
          this.stimuli.push(['finished'])

          // save stimuli queue so that the observer will not lose progress if they refresh the page
          const stimuliQueue = JSON.stringify(this.stimuli)
          localStorage.setItem(`${this.experiment.id}-stimuliQueue`, stimuliQueue)

          this.countTotalComparisons()

          this.resetProgress()
          this.getProgress()

          this.next()

          this.calculateLayout()
        } else {
          alert('Something went wrong. Could not start the experiment.')
        }
      }).catch(err => {
        alert(err)
      })
    },

    /**
     * Load the next image queue stimuli, or instructions.
     */
    async next () {
      // Have we reached the end?
      if (this.stimuli[this.typeIndex][0] === 'finished') {
        this.onFinish()
        return
      }

      // if the current experiment sequence is a picture queue
      if (this.stimuli[this.typeIndex][0].hasOwnProperty('picture_queue_id') && this.stimuli[this.typeIndex][0].picture_queue_id !== null) {
        // The first time we want to load the images even though user has not selected anything
        if (this.loadNextImages === true) {
          // this.focusSelect()

          await this.loadStimuli()
          ++this.imagePairIndex

          ++this.index

          // move on to the next picture sequence
          if (this.stimuli[this.typeIndex][this.sequenceIndex].stimuli.length === this.imagePairIndex) {
            this.imagePairIndex = 0
            ++this.sequenceIndex
          }

          // move on to the next experiment sequence
          if (this.stimuli[this.typeIndex].length === this.sequenceIndex) {
            this.sequenceIndex = 0
            this.imagePairIndex = 0
            ++this.typeIndex
          }

          this.loadNextImages = false
        }

        // don't do anything unless all categories has been selected
        if (this.selectedCategoryLeft !== null && this.selectedCategoryMiddle !== null && this.selectedCategoryRight !== null) {
          this.disableNextBtn = true

          // record the current time
          let endTime = new Date()
          // get the number of seconds between endTime and startTime
          let seconds = datetimeToSeconds(this.startTime, endTime)

          // send results to db
          let response = await this.store(
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture.id,
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture.id,
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][2].picture.id,
            seconds
          )

          if (response.data === 'result_stored') {
            this.selectedCategoryLeft = null
            this.selectedCategoryMiddle = null
            this.selectedCategoryRight = null
            if (this.experiment.artifact_marking) this.shapes = {}
            this.saveProgress()

            await this.loadStimuli()
            ++this.index

            ++this.imagePairIndex

            // move on to the next picture sequence
            if (this.stimuli[this.typeIndex][this.sequenceIndex].stimuli.length === this.imagePairIndex) {
              this.imagePairIndex = 0
              ++this.sequenceIndex
            }

            // move on to the next experiment sequence
            if (this.stimuli[this.typeIndex].length === this.sequenceIndex) {
              this.sequenceIndex = 0
              this.imagePairIndex = 0
              ++this.typeIndex
            }

            // this.focusSelect()
          } else {
            alert(`
              'Could not save your answer. Please try again. If the problem
              persist please contact the researcher.'
            `)
          }

          this.disableNextBtn = false
        }
      } else if (this.stimuli[this.typeIndex][0].hasOwnProperty('instruction_id') && this.stimuli[this.typeIndex][0].instruction_id !== null) {
        this.imageLeft = ''
        this.imageMiddle = ''
        this.imageRight = ''
        this.originalImage = ''
        this.loadNextImages = true

        this.instructionText = this.stimuli[this.typeIndex][this.sequenceIndex].instruction.description
        this.instructionDialog = true

        this.saveProgress()

        ++this.sequenceIndex
        // move on to the next experiment sequence
        if (this.stimuli[this.typeIndex].length === this.sequenceIndex) {
          this.sequenceIndex = 0
          ++this.typeIndex
        }
      }
    },

    async loadStimuli () {
      // set original
      if (
        this.stimuli[this.typeIndex][this.sequenceIndex].hasOwnProperty('picture_set') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.hasOwnProperty('pictures') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].original === 1
      ) {
        this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0].path
      } else {
        this.originalImage = ''
      }

      const imgLeft = new Image()
      imgLeft.src = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture.path
      imgLeft.onload = () => {
        this.isLoadLeft = false
        this.imageLeft = imgLeft.src
        // this.leftCanvas = { path: imgLeft.src, image: this.stimuli[this.index] }

        window.setTimeout(() => {
          this.isLoadLeft = true
          // starts or overrides existing timer
          this.startTime = new Date()
          this.disableNextBtn = false
          // console.log('left - ' + this.imageLeft)
        }, this.experiment.delay)
      }

      const imgMiddle = new Image()
      imgMiddle.src = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture.path
      imgMiddle.onload = () => {
        this.isLoadMiddle = false
        this.imageMiddle = imgMiddle.src
        // this.middleCanvas = { path: imgMiddle.src, image: this.stimuli[this.index + 1] }
        window.setTimeout(() => {
          this.isLoadMiddle = true
          // starts or overrides existing timer
          this.startTime = new Date()
          this.disableNextBtn = false
          // console.log('middle - ' + this.imageMiddle)
        }, this.experiment.delay)
      }

      const imgRight = new Image()
      imgRight.src = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][2].picture.path
      imgRight.onload = () => {
        this.isLoadRight = false
        this.imageRight = imgRight.src
        // this.rightCanvas = { path: imgRight.src, image: this.stimuli[this.index + 2] }
        window.setTimeout(() => {
          this.isLoadRight = true
          // starts or overrides existing timer
          this.startTime = new Date()
          this.disableNextBtn = false
          // console.log('right - ' + this.imageRight)
        }, this.experiment.delay)
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
        picture_id_left:      pictureIdLeft,
        picture_id_middle:    pictureIdMiddle,
        picture_id_right:     pictureIdRight,
        client_side_timer:    clientSideTimer,
        chose_none: 0
      }
      /* eslint-enable */

      return this.$axios.post('/result-triplets', data)
    },

    calculateLayout () {
      this.$nextTick(() => {
        let navMain = 30
        let navMarker = this.$refs.navMarker.offsetHeight
        let navAction = this.$refs.navAction.offsetHeight
        let navNext = this.$refs.navAction.offsetHeight
        let minus = navMain + navNext + navAction + navMarker

        var height = document.body.scrollHeight - minus - 30
        this.$refs.images.style.maxHeight = height + 'px'
      })
    },

    /**
     * Loop through the stimuli array and count how many picture pairs we have.
     */
    countTotalComparisons () {
      let stimuliCount = this.stimuli.reduce((accumulator, currentValue) => {
        if (currentValue[0].hasOwnProperty('stimuli')) {
          let total = currentValue.reduce((accu, current) => {
            return accu + current.stimuli.length
          }, 0)
          return accumulator + total
        } else {
          return accumulator
        }
      }, 0)

      this.totalComparisons = stimuliCount
    },

    onFinish () {
      this.originalImage = ''
      this.imageLeft = ''
      this.imageMiddle = ''
      this.imageRight = ''

      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      this.removeProgress()
      this.finished = true
    },

    abort () {
      this.removeProgress()
      this.abortDialog = true
      this.$router.push('/observer')
    },

    saveProgress () {
      localStorage.setItem(`${this.experiment.id}-index`, this.index)
      localStorage.setItem(`${this.experiment.id}-imagePairIndex`, this.imagePairIndex)
      localStorage.setItem(`${this.experiment.id}-sequenceIndex`, this.sequenceIndex)
      localStorage.setItem(`${this.experiment.id}-typeIndex`, this.typeIndex)
    },

    getProgress () {
      this.experimentResult = Number(localStorage.getItem(`${this.experiment.id}-experimentResult`))
      this.index            = Number(localStorage.getItem(`${this.experiment.id}-index`))
      this.imagePairIndex   = Number(localStorage.getItem(`${this.experiment.id}-imagePairIndex`))
      this.sequenceIndex    = Number(localStorage.getItem(`${this.experiment.id}-sequenceIndex`))
      this.typeIndex        = Number(localStorage.getItem(`${this.experiment.id}-typeIndex`))
      this.stimuli          = JSON.parse(localStorage.getItem(`${this.experiment.id}-stimuliQueue`))
    },

    resetProgress () {
      localStorage.setItem(`${this.experiment.id}-index`, 0)
      localStorage.setItem(`${this.experiment.id}-imagePairIndex`, 0)
      localStorage.setItem(`${this.experiment.id}-sequenceIndex`, 0)
      localStorage.setItem(`${this.experiment.id}-typeIndex`, 0)
    },

    removeProgress () {
      localStorage.removeItem(`${this.experiment.id}-index`)
      localStorage.removeItem(`${this.experiment.id}-experimentResult`)
      localStorage.removeItem(`${this.experiment.id}-stimuliQueue`)
      localStorage.removeItem(`${this.experiment.id}-imagePairIndex`)
      localStorage.removeItem(`${this.experiment.id}-sequenceIndex`)
      localStorage.removeItem(`${this.experiment.id}-typeIndex`)
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
