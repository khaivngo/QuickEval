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

    <v-row ref="images" class="fill-height justify-center ml-3 mt-0 mb-0 mr-3 pa-0">
      <v-col
        class="picture-container fill-height mt-0 mb-0 pb-2"
        :class="selectedRadio === 'left' ? 'selected' : ''"
        :style="`margin-right: ${experiment.stimuli_spacing}px;`"
      >
        <!-- @click="selectedRadio = 'left'" -->
        <div class="panzoom d-flex justify-center align-center">
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
        class="picture-container fill-height mt-0 mb-0 pb-2"
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
            <video loop controls style="width: 100%; display: block;" ref="videoPlayer" class="video-player">
              <source :src="originalImage" :type="'video/'+originalExtension">
              Your browser does not support the video tag.
            </video>
          </div>
        </div>
      </v-col>

      <v-col
        class="picture-container fill-height mt-0 mb-0 pb-2"
        :class="selectedRadio === 'right' ? 'selected' : ''"
      >
        <!-- @click="selectedRadio = 'right'" -->
        <div class="panzoom d-flex justify-center align-center">
          <div v-if="!experiment.artifact_marking" class="stimuli-container2" style="position: relative;">
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

    <v-radio-group ref="navAction" v-model="selectedRadio">
      <v-progress-circular
        v-show="disableNextBtn"
        indeterminate
        :value="20" :size="30" :width="3"
        color="#3B3B3B"
        style="position: absolute; left: 48%;"
      ></v-progress-circular>

      <v-row class="justify-center align-center pt-0 pl-0 pr-0 pb-4 ma-0">
        <v-col class="pa-0 mt-0 pt-1">
          <div class="d-flex justify-center pa-0 mt-0">
            <v-icon class="mr-1">mdi-arrow-left-box</v-icon>
            <v-radio
              color="default" value="left" class="scaled"
              :disabled="disableNextBtn"
            ></v-radio>
          </div>
        </v-col>
        <v-col v-show="originalImage" class="pa-0 mt-0">
          <div class="d-flex justify-center"></div>
        </v-col>
        <v-col class="pa-0 mt-0 pt-1">
          <div class="d-flex justify-center pa-0 mt-0">
            <v-radio
              color="default" value="right" class="scaled"
              :disabled="disableNextBtn"
            ></v-radio>
            <v-icon class="mb-2" style="margin-left: 10px;">mdi-arrow-right-box</v-icon>
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

      cols: 6,

      instructionText: 'Rate the images.',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      originalType: '',
      originalExtension: '',
      leftImage: '',
      leftType: '', // change
      rightImage: '',
      rightType: '', // change
      leftCanvas: '',
      rightCanvas: '',
      leftExtension: '',
      rightExtension: '',
      videoFormats: ['m4p', 'webm', '3g2', '3gp', 'aaf', 'asf', 'avchd', 'avi', 'drc', 'flv', 'm2v', 'm3u8', 'm4v', 'mkv', 'mng', 'mov', 'mp2', 'mp4', 'mpe', 'mpeg', 'mpg', 'mpv', 'mxf', 'nsv', 'ogg', 'ogv', 'qt', 'rm', 'rmvb', 'roq', 'svi', 'vob', 'wmv', 'yuv'],
      imageFormats: ['jpg', 'jpeg', 'jpe', 'jif', 'jfif', 'jfi', 'png', 'gif', 'webp', 'tiff', 'tif', 'psd', 'raw', 'arw', 'cr2', 'nrw', 'k25', 'bmp', 'dib', 'heif', 'heic', 'ind', 'indd', 'indt', 'jp2', 'j2k', 'jpf', 'jpx', 'jpm', 'mj2', 'svg', 'svgz', 'ai', 'eps', 'pdf'],

      timeElapsed: null,

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
        // arrow left
        if (e.keyCode === 37) {
          if (this.disableNextBtn === false) {
            this.selectedRadio = 'left'
          }
        }

        // arrow right
        if (e.keyCode === 39) {
          if (this.disableNextBtn === false) {
            this.selectedRadio = 'right'
          }
        }

        // esc
        if (e.keyCode === 27) {
          this.abort()
        }
      })
    })
  },

  // onDestroy () {
  //   document.removeEventListener('keydown', nameOfFunction)
  // },

  watch: {
    originalImage () {
      this.calculateLayout()
    },
    selectedRadio (newVal) {
      if (newVal !== null && ['left', 'right'].includes(newVal)) {
        this.saveAnswer()
      }
    }
  },

  destroyed () {
    // window.removeEventListener('keydown', functionName)
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

    loadInstructions () {
      this.leftImage     = ''
      this.leftType      = ''
      this.originalImage = ''
      this.rightImage    = ''
      this.rightType     = ''

      this.instructionText = this.stimuli[this.typeIndex][this.sequenceIndex].instruction.description
      this.instructionDialog = true

      this.saveProgress()
      this.disableNextBtn = false

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

      // set original if it exists for the current experiment sequence
      if (
        this.stimuli[this.typeIndex][this.sequenceIndex].hasOwnProperty('picture_set') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.hasOwnProperty('pictures') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].original === 1
      ) {
        this.originalExtension = this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.pictures[0].extension
        if (this.allowedImageFormat(this.originalExtension)) {
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

      // prepare to load reproduction images
      var images = [
        {
          img: new Image(),
          path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture.path,
          extension: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture.extension
        },
        {
          img: new Image(),
          path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture.path,
          extension: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture.extension
        }
      ]

      var showLoadedStimuli = () => {
        let container = document.querySelector('.stimuli-container1')
        let prevImage = document.querySelector('.stimuli-container1 .stimulus1')
        if (prevImage) {
          container.removeChild(prevImage)
        }
        container.appendChild(this.leftImage)

        let container2 = document.querySelector('.stimuli-container2')
        let prevImage2 = document.querySelector('.stimuli-container2 .stimulus2')
        if (prevImage2) {
          container2.removeChild(prevImage2)
        }
        container2.appendChild(this.rightImage)

        window.setTimeout(() => {
          let newNode = document.querySelector('.stimuli-container1 .stimulus1')
          let newNode2 = document.querySelector('.stimuli-container2 .stimulus2')
          newNode.classList.remove('hide')
          newNode2.classList.remove('hide')

          this.startTime = new Date()

          if (hideTimer) {
            window.hideTimeout = window.setTimeout(() => {
              newNode.classList.add('hide')
              newNode2.classList.add('hide')
            }, hideTimer)
          }

          // this.focusSelect()
          this.disableNextBtn = false
        }, this.experiment.delay)
      }

      var stimuliLoaded = 0
      // this.leftType  = this.allowedImageFormat(images[0].extension) ? 'image' : 'video'
      // this.rightType = this.allowedVideoFormat(images[1].extension) ? 'video' : 'image'
      // is the left image of type video or image?
      if (this.allowedImageFormat(images[0].extension)) {
        var tempImage = document.createElement('img')
        tempImage.src = images[0].path
        // tempImage.style.width = '100%'
        tempImage.classList.add('stimulus1')
        tempImage.classList.add('hide')
        this.leftImage = tempImage

        var loadNewImage = () => {
          tempImage.removeEventListener('load', loadNewImage, false)

          ++stimuliLoaded
          if (stimuliLoaded === images.length) {
            showLoadedStimuli()
          }
        }

        tempImage.addEventListener('load', loadNewImage, false)
        // this.elem = this.$refs.videoPlayer[0].$el
      } else if (this.allowedVideoFormat(images[0].extension)) {
        // create new video element and start loading stimulus
        var tempVideo = document.createElement('video')
        tempVideo.src = images[0].path
        tempVideo.autoplay = true // replace with video.play() for more control?
        tempVideo.loop = true
        tempVideo.controls = true
        tempVideo.style.width = '100%'
        tempVideo.classList.add('stimulus1')
        tempVideo.classList.add('hide')
        this.leftImage = tempVideo

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
      // is the right image of type video or image?
      if (this.allowedImageFormat(images[1].extension)) {
        var tempImage2 = document.createElement('img')
        tempImage2.src = images[1].path
        tempImage2.classList.add('stimulus2')
        tempImage2.classList.add('hide')
        this.rightImage = tempImage2

        let loadNewImage = () => {
          tempImage2.removeEventListener('load', loadNewImage, false)

          ++stimuliLoaded
          if (stimuliLoaded === images.length) {
            showLoadedStimuli()
          }
        }

        tempImage2.addEventListener('load', loadNewImage, false)
        // this.elem = this.$refs.videoPlayer[0].$el
      } else if (this.allowedVideoFormat(images[1].extension)) {
        // create new video element and start loading stimulus
        var tempVideo2 = document.createElement('video')
        tempVideo2.src = images[1].path
        tempVideo2.autoplay = true
        tempVideo2.loop = true
        tempVideo2.controls = true
        tempVideo2.style.width = '100%'
        tempVideo2.classList.add('stimulus2')
        tempVideo2.classList.add('hide')
        this.rightImage = tempVideo2

        let loadNewVideo = () => {
          // this event may be called multiple times on some browsers, therefore remove it
          tempVideo2.removeEventListener('canplaythrough', loadNewVideo, false)

          ++stimuliLoaded
          if (stimuliLoaded === images.length) {
            showLoadedStimuli()
          }
        }

        tempVideo2.load()
        tempVideo2.addEventListener('canplaythrough', loadNewVideo, false)
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

        try {
          await this.store(
            selectedStimuli,
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][0].picture,
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex][1].picture,
            seconds
          )

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
        } catch (err) {
          alert(`Could not save your answer. Check your internet connection and please try again. If the problem persist please contact the researcher.`)
          this.disableNextBtn = false
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

    playAllVideos (e) {
      let playButton = e.target
      var videos = document.querySelectorAll('video')
      // console.log(this.$refs.videoPlayer)
      // let video2 = this.$refs.videoPlayer2
      console.log(videos)
      for (const video of videos) {
        if (video.paused === true) {
          video.play()
          // playButton.innerHTML = 'Pause'
          playButton.style.opacity = 0
        } else {
          video.pause()
          // playButton.innerHTML = 'Play'
          playButton.style.opacity = 1
        }
      }

      // var playButton = document.getElementById("play_button")
      // // Event listener for the play/pause button
      // playButton.addEventListener("click", function() {
      // })
    },

    /**
     * @param string
     * @returns boolean
     */
    allowedImageFormat (extension) {
      return this.imageFormats.includes(extension.toLowerCase())
    },

    /**
     * @param string
     * @returns Boolean
     */
    allowedVideoFormat (extension) {
      return this.videoFormats.includes(extension.toLowerCase())
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
        // console.log(this.$refs.image)
        // this.$refs.image.style.height = height + 'px'
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
      this.disableNextBtn = false
      this.originalImage = ''
      this.leftImage = ''
      this.leftType = ''
      this.rightImage = ''
      this.rightType = ''

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
