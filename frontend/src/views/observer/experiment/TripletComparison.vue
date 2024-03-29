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
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="headline">Do you want to quit the experiment?</v-card-title>
            <v-card-text></v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="default darken-1" text @click="abortDialog = false">No, Continue</v-btn>
              <v-btn color="red darken-1" text @click="abort">Yes, Quit</v-btn>
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

    <v-row ref="images" class="fill-height justify-center ml-3 mt-0 mb-0 mr-3 pa-0 pt-0">
      <v-col
        class="picture-container fill-height mt-2"
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
        v-show="originalImage"
      >
        <div class="panzoom d-flex justify-center align-center">
          <img
            v-if="originalType === 'image'"
            id="picture-original"
            class="picture"
            :src="originalImage"
          />
          <div v-if="originalType === 'video'" style="position: relative;">
            <video
              :src="originalImage"
              autoplay loop controls
              style="display: block;"
            ></video>
          </div>
        </div>
      </v-col>

      <v-col
        class="picture-container fill-height mt-2"
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
      >
        <div class="panzoom d-flex justify-center align-center">
          <!-- <img
            v-if="!experiment.artifact_marking"
            id="picture-left" class="picture"
            :class="isLoadLeft === false ? 'hide' : ''"
            :src="imageLeft"
          /> -->
          <div v-if="!experiment.artifact_marking" class="stimuli-container1" style="position: relative;">
            <!-- load stimulus here -->
          </div>
          <div v-if="experiment.artifact_marking">
            <ArtifactMarker
              @updated="drawn"
              :imageURL="leftCanvas"
              :tool="drawingTool"
            />
          </div>
        </div>
      </v-col>

      <v-col
        class="picture-container fill-height mt-2"
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
      >
        <div class="panzoom d-flex justify-center align-center">
          <!-- <img
            v-if="!experiment.artifact_marking"
            id="picture-middle" class="picture"
            :class="isLoadMiddle === false ? 'hide' : ''"
            :src="imageMiddle"
          /> -->
          <div v-if="!experiment.artifact_marking" class="stimuli-container2" style="position: relative;">
            <!-- load stimulus here -->
          </div>
          <div v-if="experiment.artifact_marking">
            <ArtifactMarker
              @updated="drawn"
              :imageURL="middleCanvas"
              :tool="drawingTool"
            />
          </div>
        </div>
      </v-col>

      <v-col class="picture-container fill-height mt-2 mb-2">
        <div class="panzoom d-flex justify-center align-center">
          <!-- <img
            v-if="!experiment.artifact_marking"
            id="picture-right" class="picture"
            :class="isLoadRight === false ? 'hide' : ''"
            :src="imageRight"
          /> -->
          <div v-if="!experiment.artifact_marking" class="stimuli-container3" style="position: relative;">
            <!-- load stimulus here -->
          </div>
          <div v-if="experiment.artifact_marking">
            <ArtifactMarker
              @updated="drawn"
              :imageURL="rightCanvas"
              :tool="drawingTool"
            />
          </div>
        </div>
      </v-col>
    </v-row>

    <v-row ref="navAction" class="justify-end pr-6 pt-2">
      <v-col
        v-show="originalImage"
        class="justify-center align-center ml-2 mr-2"
      >
        <h4 class="subtitle-1 pb-0 mb-0 text-center">
          Original
        </h4>
      </v-col>
      <v-col class="ml-2 mr-2">
        <v-row class="pa-0 ma-0 justify-center">
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
        </v-row>
      </v-col>

      <v-col class="justify-center align-center ml-2 mr-2">
        <v-row class="pa-0 ma-0 justify-center">
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
        </v-row>
      </v-col>

      <v-col class="justify-center align-center ml-2 mr-2">
        <v-row class="pa-0 ma-0 justify-center">
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
        </v-row>
      </v-col>
    </v-row>

    <v-layout ref="navNext" class="justify-end pt-4 pr-6">
      <v-btn
        color="#D9D9D9"
        @click="saveAnswer()"
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
import mixin from '@/mixins/FileFormats.js'

export default {
  name: 'triplet-experiment-view',

  components: {
    FinishedDialog,
    ArtifactMarkerToolbar,
    ArtifactMarker
  },

  mixins: [mixin],

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
      originalType: '',

      activeDOMStimuli: [],
      currentStimuli: [],
      currentOriginal: [],

      typeIndex: 0,
      sequenceIndex: 0,
      imagePairIndex: 0,
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

      window.addEventListener('keydown', this.onKeyPress)
    })
  },

  destroyed () {
    window.removeEventListener('keydown', this.onKeyPress)
  },

  watch: {
    originalImage () {
      this.calculateLayout()
    }
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
      this.nextStep()
    },

    continueExistingExperiment () {
      this.getProgress()
      this.countTotalComparisons()
      this.nextStep()
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

          this.nextStep()

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
    async nextStep () {
      // Have we reached the end?
      if (this.stimuli[this.typeIndex][0] === 'finished') {
        this.onFinish()
        return
      }

      // if the current experiment sequence is a picture queue
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
      // don't do anything unless all categories has been selected
      if (
        this.selectedCategoryLeft !== null &&
        this.selectedCategoryMiddle !== null &&
        this.selectedCategoryRight !== null
      ) {
        this.disableNextBtn = true

        // record the current time
        let endTime = new Date()
        // get the number of seconds between endTime and startTime
        let seconds = datetimeToSeconds(this.startTime, endTime)

        try {
          // send results to db
          // let response =
          await this.store(
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture.id,
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture.id,
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][2].picture.id,
            seconds
          )

          // reset stuff
          this.selectedCategoryLeft = null
          this.selectedCategoryMiddle = null
          this.selectedCategoryRight = null
          this.shapes = {}

          // increment indexes
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

          this.saveProgress()
          this.nextStep()
        } catch (err) {
          alert(`Could not save your answer. Check your internet connection and please try again. If the problem persist please contact the researcher.`)
          this.disableNextBtn = false
        }

        this.disableNextBtn = false
      }
    },

    loadInstructions () {
      this.originalImage = ''
      this.originalType = ''
      this.activeDOMStimuli = []
      this.currentStimuli = []
      this.currentOriginal = []

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
      var stimuliLoaded = 0

      // clear the hide image timer to reset and ensure the timer always starts from the correct time
      // or is wiped if we move to a new image set
      if (window.hideTimeout) {
        window.clearTimeout(window.hideTimeout)
      }

      var hideTimer = this.stimuli[this.typeIndex][this.sequenceIndex].hide_image_timer

      this.currentStimuli  = this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex]

      // we use a object because sometimes the image is the same image but we still want
      // to trigger watch in child components
      if (this.experiment.artifact_marking) {
        this.leftCanvas = {
          image: this.currentStimuli[0].picture,
          path: this.$UPLOADS_FOLDER + this.currentStimuli[0].picture.path
        }
        this.middleCanvas = {
          image: this.currentStimuli[1].picture,
          path: this.$UPLOADS_FOLDER + this.currentStimuli[1].picture.path
        }
        this.rightCanvas = {
          image: this.currentStimuli[2].picture,
          path: this.$UPLOADS_FOLDER + this.currentStimuli[2].picture.path
        }
      }

      // set original if it exists for the current experiment sequence
      if (
        this.stimuli[this.typeIndex][this.sequenceIndex].hasOwnProperty('picture_set') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.hasOwnProperty('pictures') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].original === 1
      ) {
        this.currentOriginal = this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0]
        this.originalType = this.isImage(this.currentOriginal.extension) ? 'image' : 'video'

        this.$nextTick(() => {
          this.originalImage = this.$UPLOADS_FOLDER + this.currentOriginal.path
        })
      } else {
        this.originalImage = ''
      }

      // prepare to load reproduction stimuli
      var images = [
        {
          path: this.$UPLOADS_FOLDER + this.currentStimuli[0].picture.path,
          extension: this.currentStimuli[0].picture.extension
        },
        {
          path: this.$UPLOADS_FOLDER + this.currentStimuli[1].picture.path,
          extension: this.currentStimuli[1].picture.extension
        },
        {
          path: this.$UPLOADS_FOLDER + this.currentStimuli[2].picture.path,
          extension: this.currentStimuli[2].picture.extension
        }
      ]

      var showLoadedStimuli = () => {
        // append the new stimuli elements into the DOM, their hidden by CSS for now
        this.activeDOMStimuli.forEach((stimuli, index) => {
          let container   = document.querySelector('.stimuli-container' + (index + 1))
          let prevStimuli = document.querySelector('.stimuli-container' + (index + 1) + ' .stimulus' + (index + 1))
          if (prevStimuli) {
            container.removeChild(prevStimuli)
          }
          container.appendChild(stimuli)
        })

        // hide the previous stimuli and set a timer for showing (unhide) the new stimuli
        window.setTimeout(() => {
          let newNode  = document.querySelector('.stimuli-container1 .stimulus1')
          let newNode2 = document.querySelector('.stimuli-container2 .stimulus2')
          let newNode3 = document.querySelector('.stimuli-container3 .stimulus3')
          newNode.classList.remove('hide')
          newNode2.classList.remove('hide')
          newNode3.classList.remove('hide')

          this.startTime = new Date()

          // if the scientist have chosen to hide the stimuli after a certain time
          // start a timeout to hide the images
          if (hideTimer) {
            window.hideTimeout = window.setTimeout(() => {
              newNode.classList.add('hide')
              newNode2.classList.add('hide')
              newNode3.classList.add('hide')
            }, hideTimer)
          }

          this.disableNextBtn = false
        }, this.experiment.delay)
      }

      images.forEach((image, i) => {
        if (this.isImage(image.extension)) {
          var tempImage = document.createElement('img')
          tempImage.src = image.path
          // tempImage.style.width = '100%'
          tempImage.display = 'block'
          tempImage.classList.add('stimulus' + (i + 1))
          tempImage.classList.add('hide')

          this.activeDOMStimuli[i] = tempImage

          var loadNewImage = () => {
            tempImage.removeEventListener('load', loadNewImage, false)

            ++stimuliLoaded
            if (stimuliLoaded === images.length) {
              showLoadedStimuli()
            }
          }

          tempImage.addEventListener('load', loadNewImage, false)
        } else if (this.isVideo(image.extension)) {
          // create new video element and start loading stimulus
          var tempVideo = document.createElement('video')
          tempVideo.src = image.path
          tempVideo.autoplay = true // replace with video.play() for more control?
          tempVideo.loop = true
          tempVideo.controls = true
          // tempVideo.style.width = '100%'
          tempVideo.display = 'block'
          tempVideo.classList.add('stimulus' + (i + 1))
          tempVideo.classList.add('hide')

          this.activeDOMStimuli[i] = tempVideo

          var loadNewVideo = () => {
            // this event may be called multiple times on some browsers, therefore remove it
            tempVideo.removeEventListener('canplaythrough', loadNewVideo, false)

            ++stimuliLoaded
            if (stimuliLoaded === images.length) {
              showLoadedStimuli()
            }
          }

          tempVideo.load()
          tempVideo.addEventListener('canplaythrough', loadNewVideo, false)
        }
      })
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
        chose_none:           0,
        artifact_marks:       this.shapes
      }
      /* eslint-enable */

      // console.log(document.querySelector('.stimuli-container1 .stimulus1').getAttribute('src').split('/').pop())
      // console.log(pictureLeft.path.split('/').pop())
      // console.log('----')
      // console.log(document.querySelector('.stimuli-container2 .stimulus2').getAttribute('src').split('/').pop())
      // console.log(pictureRight.path.split('/').pop())

      return this.$axios.post('/result-triplets', data)
    },

    onKeyPress (e) {
      // if (e.keyCode === 13 || e.keyCode === 39 || e.keyCode === 32) {
      // enter / arrow right / space
      //   if (this.selectedCategoryLeft !== null && this.selectedCategoryMiddle !== null && this.selectedCategoryRight !== null) {
      //     this.next()
      //   }
      // }

      // esc
      if (e.code === 'Escape') {
        this.abortDialog = true
      }
    },

    calculateLayout () {
      this.$nextTick(() => {
        let navMain = 30
        let navMarker = this.$refs.navMarker.offsetHeight
        let navAction = this.$refs.navAction.offsetHeight
        let navNext = this.$refs.navAction.offsetHeight
        let minus = navMain + navNext + navAction + navMarker

        var height = document.body.scrollHeight - minus - 30
        this.$refs.images.style.height = height + 'px'
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
      this.originalType = ''
      this.activeDOMStimuli = []
      this.currentStimuli = []
      this.currentOriginal = []

      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      this.removeProgress()
      this.finished = true
    },

    abort () {
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
