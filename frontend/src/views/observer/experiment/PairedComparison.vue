<template>
  <div class="qe-wrapper" :style="'background-color: #' + experiment.background_colour" @keydown.esc="alert('esc')">
    <v-toolbar ref="navMain" flat height="30" color="#282828">
      <v-toolbar-items>
        <v-dialog persistent v-model="howToDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn text dark color="#D9D9D9" v-on="on">
              How to
            </v-btn>
          </template>
          <v-card style="background-color: grey; color: #fff;">
            <v-card-title class="headline">
              How to
            </v-card-title>

            <v-card-text style="color: #fff;">
              <h3 class="subtitle-1 mt-4">
                1. Images can be moved around. To do so click and hold down mouse on the image, then move around.
              </h3>

              <h4 class="subtitle-1 mt-4">
                2. Press left arrow keyboard key to select left image.
                Press right arrow keyboard key to select right image.
              </h4>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="#333 "
                dark
                @click="howToDialog = false"
              >
                Close
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar-items>

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

            <v-card-text v-html="instructionText" style="color: #fff;" class="subtitle-1 mt-4"></v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="#333"
                dark
                @click="closeAndNext"
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
              <!-- <v-icon right small>mdi-logout</v-icon> -->
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

    <v-layout ref="titles" pa-0 ma-0 justify-center>
      <h4
        :class="(originalImage !== '') ? 'show' : 'hide'"
        class="subtitle-1 pt-3"
        style="padding-bottom: 0px; margin-bottom: 0;"
      >
        Original
      </h4>
    </v-layout>

    <v-layout ref="images" fill-height ml-3 mt-0 mb-0 mr-3 pa-0 pt-2 justify-center>
      <v-flex
        mt-0 mb-0 pb-2
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
        class="picture-container"
        :class="selectedRadio === 'left' ? 'selected' : ''"
        @click="selectedRadio = 'left'"
      >
        <div class="panzoom d-flex justify-center align-center">
          <img
            v-if="!experiment.artifact_marking"
            id="picture-left"
            class="picture"
            :class="isLoadLeft === false ? 'hide' : ''"
            :src="leftImage"
            tabindex="0"
          />
          <ArtifactMarker
            v-if="experiment.artifact_marking"
            @updated="drawn"
            :imageURL="leftCanvas"
            :tool="drawingTool"
          />
        </div>
      </v-flex>

      <v-flex
        mt-0 mb-0 pb-2
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
        class="picture-container"
        v-show="originalImage"
      >
        <div class="panzoom d-flex justify-center align-center">
          <img
            id="picture-original"
            class="picture"
            :src="originalImage"
          />
        </div>
      </v-flex>

      <v-flex
        class="picture-container"
        mt-0 mb-0 pb-2
        :class="selectedRadio === 'right' ? 'selected' : ''"
        @click="selectedRadio = 'right'"
      >
        <div class="panzoom d-flex justify-center align-center">
          <img
            v-if="!experiment.artifact_marking"
            id="picture-right"
            class="picture"
            :class="isLoadRight === false ? 'hide' : ''"
            :src="rightImage"
            tabindex="0"
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

    <!-- <v-layout ref="navAction"> -->
    <v-radio-group ref="navAction" v-model="selectedRadio">
      <v-row class="pt-0 pl-0 pr-0 pb-4 ma-0 align-center">
        <v-col class="pa-0 mt-0 pt-1">
          <div class="d-flex justify-center pa-0 mt-0">
            <!-- <v-icon class="mr-2">mdi-arrow-left-box</v-icon> -->
            <!-- left -->
            <v-radio color="default" value="left" class="scaled"></v-radio>
          </div>
        </v-col>
        <v-col cols="auto" class="pa-0 mt-0">
          <div class="d-flex justify-center">
            <v-btn
              :disabled="selectedRadio === null || disableNextBtn"
              @click="saveAnswer()"
              :loading="disableNextBtn"
              color="#D9D9D9"
            >
              <!-- :disabled="noneSelected" -->
              <span class="ml-1">next</span>
              <!-- next -->
              <v-icon>mdi-chevron-right</v-icon>
            </v-btn>
          </div>
        </v-col>
        <v-col class="pa-0 mt-0 pt-1">
          <div class="d-flex justify-center pa-0 mt-0">
            <v-radio color="default" value="right" class="scaled"></v-radio>
            <!-- right -->
            <!-- <v-icon class="ml-2 mb-2">mdi-arrow-right-box</v-icon> -->
          </div>
        </v-col>
      </v-row>
    </v-radio-group>

    <FinishedDialog :show="finished"/>
  </div>
</template>

<script>
// import Panzoom from '@panzoom/panzoom'
// import { store, mutations } from '@/store.js'
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'
import ArtifactMarkerToolbar from '@/components/ArtifactMarkerToolbar'
import ArtifactMarker from '@/components/ArtifactMarker'

export default {
  name: 'paired-experiment-view',

  components: {
    FinishedDialog,
    ArtifactMarkerToolbar,
    ArtifactMarker
  },

  data () {
    return {
      experiment: {
        id: null,
        show_original: null,
        stimuli_spacing: 15,
        background_colour: '808080',
        delay: 200
      },

      howToDialog: false,

      stimuli: [],

      selectedRadio: null,

      index: 0,
      typeIndex: 0,
      sequenceIndex: 0,
      imagePairIndex: 0,
      totalComparisons: 0,
      experimentResult: null,

      isLoadLeft: false,
      isLoadRight: false,

      selectedStimuli: null,

      disableNextBtn: false,

      instructionText: 'Rate the images.',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      leftImage: '',
      rightImage: '',
      leftCanvas: '',
      rightCanvas: '',

      timeElapsed: null,

      firstImages: 0,

      shapes: {},
      drawingTool: ''
    }
  },

  /**
   * Fetch experiment meta data. Then determine if this is the users first time for this
   * experiment. If so, fetch new stimuli queue, if not use existing from localStorage.
   * Initialize the panzoom plugin for image container, and set experiment keyboard shortcuts as well.
   */
  created () {
    this.getExperiment(this.$route.params.id).then(response => {
      this.experiment = response.data

      /* eslint-env jquery */
      $(document).ready(function () {
        (function () {
          var $pictureContainer = $('.picture-container')

          $pictureContainer.find('.panzoom').panzoom({
            $set: $pictureContainer.find('.panzoom'), // moves the images in unison
            minScale: 1,
            maxScale: 1,
            $reset: $('.panning-reset')
          }).panzoom('zoom')
        })()
      })

      // if localStorage does not exists for this experiment fetch new data
      const exists = Number(localStorage.getItem(`${this.experiment.id}-stimuliQueue`))
      if (exists === null || exists === 0) {
        this.startNewExperiment()
      } else {
        this.continueExistingExperiment()
      }

      // create some hotkeys
      window.addEventListener('keydown', (e) => {
        // enter / space
        if (e.keyCode === 13 || e.keyCode === 32) {
          if (this.selectedRadio !== null) {
            // this.nextStep()
            this.saveAnswer()
          }
        }

        // arrow left
        if (e.keyCode === 37) {
          if (this.disableNextBtn === false) {
            this.selectedRadio = 'left'
            this.saveAnswer()
          }
        }

        // arrow right
        if (e.keyCode === 39) {
          if (this.disableNextBtn === false) {
            this.selectedRadio = 'right'
            this.saveAnswer()
          }
        }

        // esc
        if (e.keyCode === 27) {
          this.abort()
        }
      })
    })
  },

  watch: {
    originalImage () {
      this.calculateLayout()
    }
  },

  destroyed () {
    // window.removeEventListener('keydown')
  },

  methods: {
    startNewExperiment () {
      this.$axios.get(`/experiment/${this.experiment.id}/start`).then((payload) => {
        if (payload) {
          this.stimuli = Object.values(payload.data)
          this.stimuli.push(['finished'])

          const stimuliQueue = JSON.stringify(this.stimuli)
          localStorage.setItem(`${this.experiment.id}-stimuliQueue`, stimuliQueue)

          this.countTotalComparisons()

          this.resetProgress()
          this.getProgress()

          this.nextStep()

          this.calculateLayout()
        } else {
          alert('Something went wrong. Could not start the experiment.')
        }
      }).catch(error => {
        alert(error)
      })
    },

    continueExistingExperiment () {
      this.getProgress()
      this.countTotalComparisons()
      this.nextStep()
      this.calculateLayout()
    },

    closeAndNext () {
      this.instructionDialog = false
      this.nextStep()
    },

    async nextStep () {
      // Have we reached the end?
      if (this.stimuli[this.typeIndex][0] === 'finished') {
        this.onFinish()
        return
      }

      if (
        this.stimuli[this.typeIndex][0].hasOwnProperty('picture_queue_id') &&
        this.stimuli[this.typeIndex][0].picture_queue_id !== null
      ) {
        await this.loadStimuli()
      } else if (
        this.stimuli[this.typeIndex][0].hasOwnProperty('instruction_id') &&
        this.stimuli[this.typeIndex][0].instruction_id !== null
      ) {
        this.loadInstructions()
      }
    },

    async saveAnswer () {
      // only do stuff if stimuli has been selected
      if (this.selectedRadio !== null) {
        this.disableNextBtn = true

        // TODO: fetch this from a active_stimuli variable instead?
        // that way we know to a higher degree that the one displayed is the same one sent
        let selectedStimuli = null
        if (this.selectedRadio === 'left')  selectedStimuli = this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture
        if (this.selectedRadio === 'right') selectedStimuli = this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture

        // record the current time
        let endTime = new Date()
        // subtract the current time with the start time (when images completed loading)
        let timeDiff = endTime - this.timeElapsed // in ms
        // strip the ms and get seconds
        timeDiff /= 1000
        let seconds = Math.round(timeDiff)

        let response = await this.store(
          selectedStimuli,
          this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture,
          this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture,
          seconds
        )

        if (response.data === 'result_stored') {
          this.selectedRadio = null
          this.shapes = {}

          ++this.imagePairIndex
          ++this.index

          // move on to the next image sequence
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

          this.saveProgress()
          this.nextStep()
        } else {
          alert(
            `Could not save your answer. Please try again. If the problem persist
            please contact the researcher.`
          )
          this.disableNextBtn = false
        }
      }
    },

    loadInstructions () {
      this.leftImage     = ''
      this.originalImage = ''
      this.rightImage    = ''

      this.instructionText = this.stimuli[this.typeIndex][this.sequenceIndex].instruction.description
      this.instructionDialog = true

      this.saveProgress()

      ++this.sequenceIndex
      // move on to the next experiment sequence
      if (this.stimuli[this.typeIndex].length === this.sequenceIndex) {
        this.sequenceIndex = 0
        ++this.typeIndex
      }
    },

    async loadStimuli () {
      // clear the hide image timer to reset and ensure the timer always starts from the correct time
      // or is wiped if we move to a new image set
      if (window.hideTimeout) {
        window.clearTimeout(window.hideTimeout)
      }

      var hideTimer = this.stimuli[this.typeIndex][this.sequenceIndex].hide_image_timer

      // set original if it exists for the current experiment sequence
      if (
        this.stimuli[this.typeIndex][this.sequenceIndex].hasOwnProperty('picture_set') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.hasOwnProperty('pictures') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].original === 1
      ) {
        this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0].path
      } else {
        this.originalImage = ''
      }

      // prepare to load reproduction images
      var images = [
        { img: new Image(), path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture.path },
        { img: new Image(), path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture.path }
      ]

      // we use a object because sometimes the image is the same image but we still want
      // to trigger watch in child components
      if (this.experiment.artifact_marking) {
        this.leftCanvas = {
          image: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture,
          path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture.path
        }
        this.rightCanvas = {
          image: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture,
          path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture.path
        }
      }

      var imageCount = images.length
      var imagesLoaded = 0
      // attach onload events to every reproduction image
      for (var i = 0; i < imageCount; i++) {
        images[i].img.src = images[i].path
        images[i].img.onload = () => {
          imagesLoaded++
          // when all images loaded
          if (imagesLoaded === imageCount) {
            // hide images
            this.isLoadLeft = false
            this.isLoadRight = false
            // this.leftImage = ''
            // this.rightImage = ''
            // then set source
            this.leftImage = images[0].img.src
            this.rightImage = images[1].img.src

            // show a blank screen inbetween image switching,
            // if scientist set up delay
            window.setTimeout(() => {
              // show left and right image
              this.isLoadLeft = true
              this.isLoadRight = true

              if (hideTimer) {
                window.hideTimeout = window.setTimeout(() => {
                  this.isLoadLeft = false
                  this.isLoadRight = false
                }, hideTimer)
              }

              // starts or overrides existing timer
              this.timeElapsed = new Date()
              this.disableNextBtn = false
            }, this.experiment.delay)
          }
        }
      }
    },

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async store (pictureSelected, pictureLeft, pictureRight, clientSideTimer) {
      let data = {
        experiment_result_id: this.experimentResult,
        picture_id_selected: pictureSelected.id,
        picture_id_left: pictureLeft.id,
        picture_id_right: pictureRight.id,
        client_side_timer: clientSideTimer,
        chose_none: 0,
        artifact_marks: this.shapes
      }

      return this.$axios.post('/result-pairs', data)
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

    /**
     * Figure out how much height room is left on the page for the image panners to fill.
     */
    calculateLayout () {
      this.$nextTick(() => {
        let navMain = 30
        let navMarker = this.$refs.navMarker.offsetHeight
        let titles = this.$refs.titles.offsetHeight
        let navAction = this.$refs.navAction.$el.offsetHeight
        let minus = navMain + titles + navMarker + navAction

        var height = document.body.scrollHeight - minus - 20
        this.$refs.images.style.height = height + 'px'
      })
    },

    drawn (shapes) {
      // shapes.uuid let's us distinguish between left and right image canvas
      this.shapes[shapes.uuid] = shapes.shapes
    },

    changedDrawingTool (string) {
      this.drawingTool = string
    },

    onFinish () {
      this.selectedRadio = null
      this.originalImage = ''
      this.leftImage = ''
      this.rightImage = ''

      // TODO: spinner while await
      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      this.removeProgress()
      this.finished = true
    },

    abort () {
      this.abortDialog = true
      this.removeProgress()
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
  height: 100%;
  display: flex;
  flex-direction: column;
  margin: 0;
  padding: 0;
}

.panzoom {
  max-height: 100%;
}

.selected {
  outline: solid 5px #282828;
}

.hide {
  opacity: 0;
  visibility: none;
}

.scaled {
  transform: scale(1.6);
  transform-origin: left;
}

.picture {
  user-select: none;
}
</style>
