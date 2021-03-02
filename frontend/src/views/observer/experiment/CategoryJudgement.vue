<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: #' + experiment.background_colour">
    <v-toolbar ref="navMain" flat height="30" color="#282828">
      <v-toolbar-items>
        <!-- <InstructionsDialog
          :show="instructionDialog"
          :text="instructionText"
        /> -->
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

      <!-- <v-toolbar-items v-if="experiment.show_progress === 1">
        <h4 class="pt-1 mr-4" style="color: #BDBDBD; padding-right: 240px;">
          {{ index }}/{{ totalComparisons }}
        </h4>
      </v-toolbar-items>

      <v-spacer></v-spacer> -->

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
      <v-flex ml-2 mr-2 xs6 justify-center align-center>
        <div class="d-flex justify-center">
          <ArtifactMarkerToolbar
            v-if="experiment.artifact_marking"
            @changed="changedDrawingTool"
            class="pt-3 pb-3"
          />
        </div>
      </v-flex>
      <v-flex ml-2 mr-2 xs6 v-if="originalImage" justify-center align-center>
      </v-flex>
    </v-layout>

    <v-layout ref="titles" pa-0 pt-4 ma-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="text-center">
        <h4 class="subtitle-1 pb-0 mb-0" v-show="originalImage">
          Original
        </h4>
      </v-flex>
    </v-layout>

    <v-layout ref="images" fill-height justify-center ml-3 mr-3 pa-0 pt-2>
      <v-flex
        :style="(originalImage) ? `margin-right: ${experiment.stimuli_spacing}px` : ''"
        mt-0 mr-2 mb-0 pb-2
        class="picture-container"
      >
        <div class="panzoom d-flex justify-center align-center">
          <img
            v-if="!experiment.artifact_marking"
            id="picture-left"
            class="picture"
            :class="isLoadLeft === false ? 'hide' : ''"
            :src="leftImage"
          />
          <ArtifactMarker
            v-if="experiment.artifact_marking"
            @updated="drawn"
            :imageURL="leftCanvas"
            :tool="drawingTool"
          />
        </div>
      </v-flex>

      <v-flex mt-0 ml-2 mb-0 pb-2 class="picture-container" v-show="originalImage">
        <div class="panzoom d-flex justify-center align-center stretch">
          <img id="picture-original" class="picture" :src="originalImage"/>
        </div>
      </v-flex>
    </v-layout>

    <v-layout ref="navAction" pt-4 pl-0 pr-0 pb-4 ma-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <v-layout pa-0 ma-0 justify-center align-center>
          <div class="pl-2 pr-2 category-select">
            <v-select
              ref="select"
              v-model="selectedCategory"
              :items="categories"
              :disabled="disableNextBtn"
              label="Select category"
              item-text="title"
              item-value="id"
              :menu-props="{ maxHeight: 400, overflowY: true }"
              hide-details
              single-line
              class="ma-0 pt-0"
            ></v-select>
          </div>

          <v-btn
            color="#D9D9D9"
            @click="next()"
            :disabled="disableNextBtn || (selectedCategory === null)"
            :loading="disableNextBtn"
            class="ml-2"
          >
            <span class="ml-1">next</span>
            <h4 class="ml-1">
              ({{ index }}/{{ totalComparisons }})
            </h4>
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </v-layout>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 v-show="originalImage" class="justify-center" justify-center align-center>
      </v-flex>
    </v-layout>

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
// import InstructionsDialog from '@/components/observer/InstructionsDialog'
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'
import ArtifactMarkerToolbar from '@/components/ArtifactMarkerToolbar'
import ArtifactMarker from '@/components/ArtifactMarker'
import { datetimeToSeconds } from '@/functions/datetimeToSeconds.js'

export default {
  name: 'category-experiment-view',

  components: {
    // InstructionsDialog,
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

      stimuli: [],

      index: 0,
      typeIndex: 0,
      sequenceIndex: 0,
      imagePairIndex: 0,
      experimentResult: null,

      totalComparisons: 0,

      categories: [],
      selectedCategory: null,
      isLoadLeft: false,

      disableNextBtn: false,

      instructionText: 'Rate the images.',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      leftImage: '',

      startTime: null,

      loadNextImages: true,

      // artifact marking
      leftCanvas: '',
      shapes: {},
      drawingTool: ''
    }
  },

  created () {
    this.getExperiment(this.$route.params.id).then(response => {
      this.experiment = response.data

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
          if (this.selectedCategory !== null && this.disableNextBtn !== true) {
            this.next()
          }
        }

        if (e.keyCode === 27) { // esc
          this.abort()
        }

        // if (e.keyCode === 40) { // down arrow
        //   this.$refs.select.activateMenu()
        // }
      })
    })
  },

  methods: {
    datetimeToSeconds: datetimeToSeconds,

    closeAndNext () {
      this.instructionDialog = false
      // this.focusSelect()
      this.next()
    },

    drawn (shapes) {
      // shapes.uuid let's us distinguish between left and right image canvas
      this.shapes[shapes.uuid] = shapes.shapes
    },

    changedDrawingTool (string) {
      this.drawingTool = string
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

        // only do stuff if stimuli has been selected
        if (this.selectedCategory !== null) {
          this.disableNextBtn = true

          // record the current time
          let endTime = new Date()
          // get the number of seconds between endTime and startTime
          let seconds = datetimeToSeconds(this.startTime, endTime)

          // send results to db
          let response = await this.store(
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture,
            seconds
          )

          if (response.data === 'result_stored') {
            this.selectedCategory = null
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
      } else if (
        this.stimuli[this.typeIndex][0].hasOwnProperty('instruction_id') &&
        this.stimuli[this.typeIndex][0].instruction_id !== null
      ) {
        this.selectedCategory = null
        this.leftImage = ''
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

      // we use a object because sometimes the image is the same image but we still want
      // to trigger watch in child components
      if (this.experiment.artifact_marking) {
        this.leftCanvas = {
          image: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture,
          path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture.path
        }
      }

      var imgLeft = new Image()
      imgLeft.src = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture.path
      imgLeft.onload = () => {
        this.isLoadLeft = false
        this.leftImage = imgLeft.src

        window.setTimeout(() => {
          this.isLoadLeft = true
          this.startTime = new Date()

          // this.focusSelect()
          this.disableNextBtn = false
        }, this.experiment.delay)
      }
    },

    calculateLayout () {
      this.$nextTick(() => {
        let navMain = 30
        let navMarker = this.$refs.navMarker.offsetHeight
        let titles = this.$refs.titles.offsetHeight
        let navAction = this.$refs.navAction.offsetHeight
        let minus = navMain + titles + navAction + navMarker

        var height = document.body.scrollHeight - minus - 20
        this.$refs.images.style.maxHeight = height + 'px'
      })
    },

    /**
     * Loop through the stimuli array and count how many picture pairs we have.
     * With this number we can display how many comparions the user have to rate.
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

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async store (pictureIdLeft, clientSideTimer) {
      let data = {
        experiment_result_id: this.experimentResult,
        category_id: this.selectedCategory,
        picture_id_left: pictureIdLeft.id,
        client_side_timer: clientSideTimer,
        chose_none: 0,
        artifact_marks: this.shapes
      }

      return this.$axios.post('/result-categories', data)
    },

    focusSelect () {
      window.setTimeout(() => {
        this.$refs.select.focus()
      }, 400)
    },

    onFinish () {
      this.originalImage = ''
      this.leftImage = ''
      this.disableNextBtn = false

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
    min-width: 250px;
    background-color: #bbb;
  }

  /* override default v-select dropdown colours */
  .theme--light.v-application {
    background-color: #bbb;
  }

  .theme--light.v-list {
    background: #bbb;
  }

  .theme--light.v-list.v-list-item__content.v-list-item__title {
    color: #fff;
  }
</style>
