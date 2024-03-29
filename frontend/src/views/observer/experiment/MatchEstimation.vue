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
          {{ index + 1}}/{{ totalComparisons }}
        </h4>
      </v-toolbar-items>

      <!-- <v-spacer></v-spacer> -->

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
      <v-flex ml-2 mr-2 xs6 class="text-center">
        <h4 class="subtitle-1 pb-0 mb-0" v-show="originalImage">
          Original
        </h4>
      </v-flex>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
      </v-flex>
    </v-layout>

    <v-row ref="images" class="fill-height justify-center ml-3 mt-0 mb-0 mr-3 pa-0">
      <v-col v-show="originalImage" class="picture-container fill-height mt-0 ml-2 mb-0 pb-2">
        <div class="panzoom d-flex justify-center align-center stretch">
          <img
            v-if="originalType === 'image'"
            id="picture-original"
            class="picture"
            :src="originalImage"
          />
          <div v-if="originalType === 'video'" style="position: relative;">
            <video loop controls autoplay style="width: 100%;" ref="videoPlayer" class="video-player">
              <source :src="originalImage" :type="'video/'+originalExtension">
              Your browser does not support the video tag.
            </video>
          </div>
        </div>
      </v-col>

      <v-col
        :style="(originalImage) ? `margin-left: ${experiment.stimuli_spacing}px` : ''"
        class="picture-container fill-height mt-0 mr-2 mb-0 pb-2"
      >
        <div class="panzoom d-flex justify-center align-center">
          <div v-if="!experiment.artifact_marking" class="stimuli-container" style="position: relative;">
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
    </v-row>

    <v-layout ref="navAction" pt-8 pl-0 pr-0 pb-4 ma-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 v-show="originalImage" class="justify-center" justify-center align-center>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <v-layout pa-0 ma-0 justify-center align-center class="flex-column">
          <div class="d-flex align-center" style="width: 100%;">
            <div class="pl-2 pr-2 flex-grow-1">
              <div class="d-flex align-center">
                <div style="max-width: 100px; text-align: center;" class="pr-4 flex-grow-0 flex-shrink-0">
                  {{ minLabel }}
                </div>
                <div class="pb-4 flex-grow-1 flex-shrink-0" style="min-width: 100px; max-width: 100%;">
                  <v-slider
                    ref="slider"
                    v-model="selectedMagnitude"
                    :disabled="disableSlider"
                    :step="interval"
                    :min="minValue"
                    :max="maxValue"
                    thumb-label="always"
                    :thumb-size="24"
                    ticks="always"
                    :tick-labels="tickLabels"
                    hide-details
                    color="#444"
                    :loading="disableSlider"
                    @change="updateActiveLabel"
                  ></v-slider>
                </div>
                <div style="max-width: 100px; text-align: center;" class="pl-4 flex-grow-0 flex-shrink-0">
                  {{ maxLabel }}
                </div>
              </div>
            </div>

            <div>
              <v-btn
                color="#D9D9D9"
                @click="saveAnswer()"
                :disabled="disableNextBtn || disableSlider"
                :loading="disableNextBtn"
                class="ml-6 mr-6"
              >
                <span class="ml-1 mr-2">next</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 26 26" style="enable-background:new 0 0 26 26;" xml:space="preserve">
                  <g>
                    <path style="fill:#333;" :style="(selectedMagnitude === null) ? 'fill: #535353;' : ''" d="M25,2H9C8.449,2,8,2.449,8,3c0,0,0,7,0,9s-2,2-2,2H1c-0.551,0-1,0.449-1,1v8c0,0.551,0.449,1,1,1h24   c0.551,0,1-0.449,1-1V3C26,2.449,25.551,2,25,2z M22,14c0,1.436-1.336,4-4,4h-3.586l1.793,1.793c0.391,0.391,0.391,1.023,0,1.414   C16.012,21.402,15.756,21.5,15.5,21.5s-0.512-0.098-0.707-0.293l-3.5-3.5c-0.391-0.391-0.391-1.023,0-1.414l3.5-3.5   c0.391-0.391,1.023-0.391,1.414,0s0.391,1.023,0,1.414L14.414,16H18c1.398,0,2-1.518,2-2v-2c0-0.553,0.447-1,1-1s1,0.447,1,1V14z"/>
                  </g>
                </svg>
              </v-btn>
            </div>
          </div>
        </v-layout>
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
import mixin from '@/mixins/FileFormats.js'

export default {
  name: 'match-experiment-view',

  components: {
    // InstructionsDialog,
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

      minValue: 0,
      maxValue: 10,
      minLabel: 'low',
      maxLabel: 'high',
      selectedMagnitude: null,
      tickLabels: [],
      interval: 1,

      stimuli: [],
      DOMStimuli: [],
      totalLoaded: 0,

      index: 0,
      typeIndex: 0,
      sequenceIndex: 0,
      imagePairIndex: 0,
      sliderIndex: null,
      experimentResult: null,

      totalComparisons: 0,

      categories: [],
      // selectedCategory: null,
      isLoadLeft: false,

      disableNextBtn: false,
      disableSlider: false,

      instructionText: 'Rate the images.',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      originalType: '',
      originalExtension: '',
      leftImage: '',
      leftType: '',
      leftExtension: '',

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

      this.$axios.get(`/experiment/${this.experiment.id}/sliders`).then((payload) => {
        this.maxValue = payload.data[0].max_value
        this.minValue = payload.data[0].min_value
        this.maxLabel = payload.data[0].max_label
        this.minLabel = payload.data[0].min_label

        // if localStorage does not exists for this experiment fetch new data
        const exists = Number(localStorage.getItem(`${this.experiment.id}-stimuliQueue`))
        if (exists === null || exists === 0) {
          this.startNewExperiment()
        } else {
          this.continueExistingExperiment()
        }
      })
    })
  },

  destroyed () {
    window.removeEventListener('keydown', this.onKeyPress)
  },

  watch: {
    originalImage () {
      this.calculateLayout()
    },
    sliderIndex (newVal, oldVal) {
      if (
        (newVal !== undefined && newVal !== null) &&
        (oldVal !== undefined && oldVal !== null)
      ) { // (oldVal !== -1 && oldVal !== null)
        this.switchStimuli(newVal, oldVal)
      }
    }
    // DOMStimuli () {
    //   // console.log(oldV, newV)
    // }
  },

  methods: {
    datetimeToSeconds: datetimeToSeconds,

    closeAndNext () {
      this.instructionDialog = false
      this.focusSlider()
      this.nextStep()
    },

    drawn (shapes) {
      // shapes.uuid let's us distinguish between left and right image canvas
      this.shapes[shapes.uuid] = shapes.shapes
    },

    changedDrawingTool (string) {
      this.drawingTool = string
    },

    continueExistingExperiment () {
      // this.sliderIndex = Math.floor(this.stimuli.length / 2)
      this.resetSliderPosition()
      // this.$nextTick(() => this.createTickLabels())
      this.createTickLabels()

      this.getProgress()
      this.countTotalComparisons()
      this.nextStep()
      this.calculateLayout()
      this.setKeyboardShortcuts()
      this.focusSlider()
    },

    startNewExperiment () {
      this.$axios.get(`/experiment/${this.experiment.id}/start`).then((payload) => {
        if (payload) {
          this.stimuli = Object.values(payload.data)
          this.stimuli.push(['finished'])

          // order the stimuli ascending by order_index
          // TODO: do this on the server
          this.stimuli.forEach(pics => {
            if (pics[0].hasOwnProperty('picture_set_id') && pics[0].picture_set_id !== null) {
              pics.forEach(p => {
                p.stimuli.sort((a, b) => parseFloat(a.picture.order_index) - parseFloat(b.picture.order_index))
              })
            }
          })

          this.resetSliderPosition()
          this.createTickLabels()

          // this.sliderIndex = Math.floor((this.minValue - this.maxValue) / 2)
          // this.sliderIndex = Math.floor((this.stimuli.length - 1) / 2)
          // since we're setting sliderIndex programatically we also have to update the selected magnitude
          // this.selectedMagnitude = 7
          // this.sliderIndex = 1
          // this.sliderIndex = this.stimuli.findIndex(item => item === label)
          // this.sliderIndex = Math.round((this.maxValue - this.minValue) / 2) - 1
          // arr[Math.floor(arr.length / 2)];

          // save stimuli queue so that the observer will not lose progress if they refresh the page
          const stimuliQueue = JSON.stringify(this.stimuli)
          localStorage.setItem(`${this.experiment.id}-stimuliQueue`, stimuliQueue)

          this.countTotalComparisons()
          this.resetProgress()
          this.getProgress()
          this.nextStep()
          this.calculateLayout()
          this.setKeyboardShortcuts()
          this.focusSlider()
        } else {
          alert('Something went wrong. Could not start the experiment.')
        }
      }).catch(err => {
        alert(err)
      })
    },

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

    /**
     * Submit the observer's choice to the server, then increment indexes and move to the next step.
     */
    async saveAnswer () {
      // only do stuff if stimuli has been selected
      if (this.selectedMagnitude !== null) {
        this.disableNextBtn = true

        // record the current time
        let endTime = new Date()
        // get the number of seconds between endTime and startTime
        let seconds = datetimeToSeconds(this.startTime, endTime)

        try {
          // send results to db
          await this.store(
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.sliderIndex].picture,
            this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0],
            seconds
          )

          this.selectedMagnitude = null
          this.shapes = {}

          ++this.index
          ++this.sequenceIndex

          // move on to the next experiment sequence
          if (this.stimuli[this.typeIndex].length === this.sequenceIndex) {
            this.sequenceIndex = 0
            ++this.typeIndex
          }

          this.saveProgress()
          this.nextStep()
          this.focusSlider()
        } catch (err) {
          alert(`Could not save your answer. Check your internet connection and please try again. If the problem persist please contact the researcher.`)
          this.disableNextBtn = false
        }
      }
    },

    loadInstructions () {
      this.selectedMagnitude = null
      this.disableNextBtn = false
      this.leftImage = ''
      this.originalImage = ''

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

    switchStimuli (newVal, oldVal) {
      console.log(newVal, oldVal)
      // disable slider...
      // this.disableSlider = true
      // show loading spinner

      // we use a object because sometimes the image is the same image but we still want
      // to trigger watch in child components
      if (this.experiment.artifact_marking) {
        this.leftCanvas = {
          image: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.sliderIndex].picture,
          path:
            this.$UPLOADS_FOLDER +
            this.stimuli[this.typeIndex][this.sequenceIndex]
              .stimuli[this.sliderIndex]
              .picture
              .path
        }
      }

      // this.disableNextBtn = true
      // this.disableSlider = true

      var hideTimer = this.stimuli[this.typeIndex][this.sequenceIndex].hide_image_timer

      this.DOMStimuli[oldVal].classList.add('hide')
      // prevImage.classList.remove('hide')
      var copy = this.DOMStimuli[this.sliderIndex]
      let container = document.querySelector('.stimuli-container')
      let prevImage = document.querySelector('.stimuli-container .stimulus')
      if (prevImage) {
        let node = document.querySelector('.stimuli-container .stimulus')
        container.removeChild(node)
      }
      container.appendChild(copy)

      window.setTimeout(() => {
        if (hideTimer) {
          window.hideTimeout = window.setTimeout(() => {
            prevImage.classList.add('hide')
          }, hideTimer)
        }

        this.DOMStimuli[newVal].classList.remove('hide')
        copy.classList.remove('hide')

        if (copy.nodeName === 'VIDEO') {
          // prevImage.play()
          copy.play()
        }

        // this.disableNextBtn = false
        this.disableSlider = false
      }, 500)
    },

    async loadStimuli () {
      this.disableSlider = true

      // clear the hide image timer to reset and ensure the timer always starts from the correct time
      // or is wiped if we move to a new image set
      if (window.hideTimeout) {
        window.clearTimeout(window.hideTimeout)
      }

      this.resetSliderPosition()

      // set or wipe original
      if (
        this.stimuli[this.typeIndex][this.sequenceIndex].hasOwnProperty('picture_set') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.hasOwnProperty('pictures')
        // this.stimuli[this.typeIndex][this.sequenceIndex].original === 1
      ) {
        this.originalExtension = this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0].extension
        if (this.isImage(this.originalExtension)) {
          this.originalType = 'image'
          this.$nextTick(() => {
            this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0].path
          })
        } else {
          this.originalType = 'video'
          this.$nextTick(() => {
            this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0].path
          })
        }
      } else {
        this.originalImage = ''
      }

      // we use a object because sometimes the image is the same image but we still want
      // to trigger watch in child components
      if (this.experiment.artifact_marking) {
        this.leftCanvas = {
          image: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.sliderIndex].picture,
          path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.sliderIndex].picture.path
        }
      }

      //
      this.totalLoaded = this.stimuli[this.typeIndex][this.sequenceIndex].stimuli.length

      this.DOMStimuli = []
      this.stimuli[this.typeIndex][this.sequenceIndex].stimuli.forEach((item, i) => {
        if (this.isVideo(item.picture.extension)) {
          // create new video element and start loading stimulus
          const tempVideo = document.createElement('video')
          tempVideo.src = this.$UPLOADS_FOLDER + item.picture.path
          // tempVideo.autoplay = true
          tempVideo.loop = true
          tempVideo.controls = true
          tempVideo.style.width = '100%'
          // tempVideo.style.pointerEvents = 'none'
          tempVideo.classList.add('stimulus')
          tempVideo.classList.add('hide')

          const loadNewVideo = () => {
            // this event may be called multiple times on some browsers, therefore remove it
            tempVideo.removeEventListener('canplaythrough', loadNewVideo, false)
            // important to use index instead of push(), so the order is correct no matter when stimuli finish loading
            this.DOMStimuli[i] = tempVideo

            // checking length alone will not work, until all images has been loaded some indexes will be empty
            // ES5 array methods skip empty slots. ES6 [].includes does not.
            if (this.totalLoaded === this.DOMStimuli.length && !this.DOMStimuli.includes(undefined)) {
              this.showFirstStimuli()
            }
          }

          tempVideo.load()
          tempVideo.addEventListener('canplaythrough', loadNewVideo, false)
        } else {
          const tempImage = document.createElement('img')
          tempImage.src = this.$UPLOADS_FOLDER + item.picture.path
          tempImage.classList.add('stimulus')
          tempImage.classList.add('hide')

          const loadNewImage = () => {
            tempImage.removeEventListener('load', loadNewImage, false)
            // important to use index instead of push(), so the order is correct no matter when stimuli finish loading
            this.DOMStimuli[i] = tempImage

            // checking length alone will not work, until all images has been loaded some indexes will be empty
            // ES5 array methods skip empty slots. ES6 [].includes does not.
            if (this.totalLoaded === this.DOMStimuli.length && !this.DOMStimuli.includes(undefined)) {
              this.showFirstStimuli()
            }
          }

          tempImage.addEventListener('load', loadNewImage, false)
        }
      })
    },

    showFirstStimuli () {
      this.totalLoaded = 0

      let container = document.querySelector('.stimuli-container')
      let prevImage = document.querySelector('.stimuli-container .stimulus')
      if (prevImage) {
        let node = document.querySelector('.stimuli-container .stimulus')
        container.removeChild(node)
      }

      container.appendChild(this.DOMStimuli[this.sliderIndex])
      this.DOMStimuli[this.sliderIndex].classList.remove('hide')
      this.disableNextBtn = false
      this.disableSlider = false
      this.startTime = new Date()
    },

    calculateLayout () {
      this.$nextTick(() => {
        let navMain = 30
        let navMarker = this.$refs.navMarker.offsetHeight
        let titles = this.$refs.titles.offsetHeight
        let navAction = this.$refs.navAction.offsetHeight
        let minus = navMain + titles + navAction + navMarker

        var height = document.body.scrollHeight - minus - 20
        this.$refs.images.style.height = height + 'px'
      })
    },

    focusSlider () {
      window.setTimeout(() => {
        // console.log(this.$refs.slider)
        // this.$refs.slider.isFocused = true
        // this.$refs.slider.$el.focus()
        // this.$refs.slider.$el.children[0].children[0].children[0].focus()
        // this.$nextTick(() => this.$refs.slider.$el.children[0].children[0].children[0].focus())
      }, 400)
    },

    setKeyboardShortcuts () {
      window.addEventListener('keydown', this.onKeyPress)
    },

    onKeyPress (e) {
      switch (e.code) {
        case 'Enter':
        case 'Space':
          if (this.selectedMagnitude !== null && this.disableNextBtn === false) {
            this.saveAnswer()
          }
          break

        case 'Escape':
          this.abortDialog = true
          break
      }
    },

    createTickLabels () {
      let ticks = this.maxValue - this.minValue + 1

      this.tickLabels = Array.from({ length: ticks }, (v, k) => {
        // if we have many steps then skip every nth label
        if (ticks > 10) {
          if (k % 2 !== 0) {
            return null
          }
        } else if (ticks > 20) {
          if (k % 4 !== 0) {
            return null
          }
        }

        // save the numbers as strings, since the number 0 will be ignored by the slider component as a label
        return '' + (this.minValue + k)
        // return this.interval > 1
        //   ? '' + ((this.minValue + k) * this.interval)
        //   : '' + (this.minValue + k)
      })

      // adjust margin and font size after labels have been added to the slider
      this.$nextTick(() => {
        const ticksLabels = document.querySelectorAll('.v-slider__ticks-container .v-slider__tick-label')
        // move first and last label slightly for better alignment
        ticksLabels[0].style.marginLeft = '-2px'
        ticksLabels[ticksLabels.length - 1].style.marginLeft = '4px'

        for (let label of ticksLabels) {
          label.style.fontSize = '12px'
        }
      })
    },

    /**
     * Set the slider position to the middle value.
     */
    resetSliderPosition () {
      this.sliderIndex = Math.floor((this.stimuli.length - 1) / 2)
      this.selectedMagnitude = this.tickLabels[this.sliderIndex]
      this.updateActiveLabel()
    },

    updateActiveLabel (label) {
      let ticks = this.maxValue - this.minValue + 1
      let tickLabels = Array.from({ length: ticks }, (v, k) => {
        return this.minValue + k
      })
      const index = tickLabels.findIndex(item => item === label)

      // console.log(label)
      if (label !== undefined && index !== -1) {
        this.sliderIndex = index // use watch() to load new images?
      } else {
        // default to the middle position
        this.sliderIndex = Math.floor((this.stimuli.length - 1) / 2)
        this.selectedMagnitude = this.tickLabels[this.sliderIndex]
      }
    },

    /**
     * Loop through the stimuli array and count how many picture groups we have.
     * With this number we can display how many comparions the user have to rate.
     */
    countTotalComparisons () {
      this.totalComparisons = this.stimuli
        // get all groups (arrays) that contain image queues
        .filter(item => item[0].hasOwnProperty('picture_queue') && item[0].picture_queue !== null)
        // count total image queues all the groups contain together
        .reduce((accu, current) => accu + current.length, 0)
    },

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async store (pictureIdLeft, pictureIdOriginal, clientSideTimer) {
      console.log(this.selectedMagnitude)
      let data = {
        experiment_result_id: this.experimentResult,
        // category_id: this.selectedCategory,
        magnitude_value: this.selectedMagnitude,
        picture_id_left: pictureIdLeft.id,
        picture_id_original: pictureIdOriginal.id,
        client_side_timer: clientSideTimer,
        chose_none: 0,
        artifact_marks: this.shapes
      }

      console.log(document.querySelector('.stimuli-container .stimulus').getAttribute('src').split('/').pop())
      console.log(pictureIdLeft.path.split('/').pop())

      return this.$axios.post('/result-match-estimations', data)
    },

    onFinish () {
      this.originalImage = ''
      this.leftImage = ''
      this.disableNextBtn = false
      this.wipeActiveStimuli()

      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      this.removeProgress()
      this.finished = true
    },

    wipeActiveStimuli () {
      const container = document.querySelector('.stimuli-container')
      const active = document.querySelector('.stimuli-container .stimulus')
      if (active) {
        container.removeChild(active)
      }
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
    min-width: 250px;
    // background-color: #bbb;
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
