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
          {{ index }}/{{ totalComparisons }}
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
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="text-center">
        <h4 class="subtitle-1 pb-0 mb-0" v-show="originalImage">
          Original
        </h4>
      </v-flex>
    </v-layout>

    <v-row ref="images" class="fill-height justify-center ml-3 mt-0 mb-0 mr-3 pa-0">
      <v-col
        :style="(originalImage) ? `margin-right: ${experiment.stimuli_spacing}px` : ''"
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
    </v-row>

    <v-layout ref="navAction" pt-4 pl-0 pr-0 pb-4 ma-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
        <v-layout pa-0 ma-0 justify-center align-center class="flex-column">
          <div class="d-flex align-center">
            <div class="pl-2 pr-2 category-select" style="position: relative;">
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
                dense
                outlined
                class="ma-0 pt-0"
                background-color="#bbb"
              ></v-select>

              <div class="d-flex align-center" style="position: absolute; top: 13px; right: 50px;">
                <svg class="ml-2 mr-1" enable-background="new 0 0 24 24" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m21.25 0h-18.5c-1.517 0-2.75 1.233-2.75 2.75v18.5c0 1.517 1.233 2.75 2.75 2.75h18.5c1.517 0 2.75-1.233 2.75-2.75v-18.5c0-1.517-1.233-2.75-2.75-2.75zm-1.81 12.043c-.118.277-.39.457-.69.457h-3.75v6.75c0 .414-.336.75-.75.75h-4.5c-.414 0-.75-.336-.75-.75v-6.75h-3.75c-.301 0-.573-.18-.69-.457-.118-.276-.058-.597.15-.813l6.75-7c.283-.293.797-.293 1.08 0l6.75 7c.209.216.268.537.15.813z"/></svg>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  height="15"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="m 2.75,24 h 18.5 C 22.767,24 24,22.767 24,21.25 V 2.75 C 24,1.233 22.767,0 21.25,0 H 2.75 C 1.233,0 0,1.233 0,2.75 v 18.5 C 0,22.767 1.233,24 2.75,24 Z M 4.56,11.957 C 4.678,11.68 4.95,11.5 5.25,11.5 H 9 V 4.75 C 9,4.336 9.336,4 9.75,4 h 4.5 C 14.664,4 15,4.336 15,4.75 v 6.75 h 3.75 c 0.301,0 0.573,0.18 0.69,0.457 0.118,0.276 0.058,0.597 -0.15,0.813 l -6.75,7 c -0.283,0.293 -0.797,0.293 -1.08,0 l -6.75,-7 C 4.501,12.554 4.442,12.233 4.56,11.957 Z"
                    id="path2"
                  />
                </svg>
              </div>
            </div>

            <div>
              <v-btn
                color="#D9D9D9"
                @click="saveAnswer()"
                :disabled="disableNextBtn || (selectedCategory === null)"
                :loading="disableNextBtn"
                class="ml-2"
              >
                <span class="ml-1 mr-2">next</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 26 26" style="enable-background:new 0 0 26 26;" xml:space="preserve">
                  <g>
                    <path style="fill:#333;" :style="(selectedCategory === null) ? 'fill: #535353;' : ''" d="M25,2H9C8.449,2,8,2.449,8,3c0,0,0,7,0,9s-2,2-2,2H1c-0.551,0-1,0.449-1,1v8c0,0.551,0.449,1,1,1h24   c0.551,0,1-0.449,1-1V3C26,2.449,25.551,2,25,2z M22,14c0,1.436-1.336,4-4,4h-3.586l1.793,1.793c0.391,0.391,0.391,1.023,0,1.414   C16.012,21.402,15.756,21.5,15.5,21.5s-0.512-0.098-0.707-0.293l-3.5-3.5c-0.391-0.391-0.391-1.023,0-1.414l3.5-3.5   c0.391-0.391,1.023-0.391,1.414,0s0.391,1.023,0,1.414L14.414,16H18c1.398,0,2-1.518,2-2v-2c0-0.553,0.447-1,1-1s1,0.447,1,1V14z"/>
                  </g>
                </svg>
              </v-btn>
            </div>
          </div>
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
import mixin from '@/mixins/FileFormats.js'

export default {
  name: 'category-experiment-view',

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
      originalType: '',
      originalExtension: '',
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

    closeAndNext () {
      this.instructionDialog = false
      this.focusSelect()
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
      this.getProgress()
      this.countTotalComparisons()
      this.nextStep()
      this.calculateLayout()
      this.setKeyboardShortcuts()
      this.focusSelect()
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

          this.setKeyboardShortcuts()
          this.focusSelect()
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
      if (this.selectedCategory !== null) {
        this.disableNextBtn = true

        // record the current time
        let endTime = new Date()
        // get the number of seconds between endTime and startTime
        let seconds = datetimeToSeconds(this.startTime, endTime)

        try {
          // send results to db
          // let response =
          await this.store(
            this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture,
            seconds
          )

          this.selectedCategory = null
          this.shapes = {}

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
          this.focusSelect()
          this.nextStep()
        } catch (err) {
          alert(`Could not save your answer. Check your internet connection and please try again. If the problem persist please contact the researcher.`)
          this.disableNextBtn = false
        }
      }
    },

    loadInstructions () {
      this.selectedCategory = null
      this.disableNextBtn = false
      this.leftImage = ''
      this.originalImage = ''

      this.instructionText = this.stimuli[this.typeIndex][this.sequenceIndex].instruction.description
      this.instructionDialog = true

      this.saveProgress()
      this.focusSelect()

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

      // set or wipe original
      if (
        this.stimuli[this.typeIndex][this.sequenceIndex].hasOwnProperty('picture_set') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].picture_set.hasOwnProperty('pictures') &&
        this.stimuli[this.typeIndex][this.sequenceIndex].original === 1
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
          image: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture,
          path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture.path
        }
      }

      // var imgLeft = new Image()
      // imgLeft.src = this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture.path
      var imgLeft = {
        img: new Image(),
        path: this.$UPLOADS_FOLDER + this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture.path,
        extension: this.stimuli[this.typeIndex][this.sequenceIndex].stimuli[this.imagePairIndex].picture.extension
      }

      if (this.isVideo(imgLeft.extension)) {
        // create new video element and start loading stimulus
        var tempVideo = document.createElement('video')
        tempVideo.src = imgLeft.path
        tempVideo.autoplay = true
        tempVideo.loop = true
        tempVideo.controls = true
        tempVideo.style.width = '100%'
        tempVideo.classList.add('stimulus')
        tempVideo.classList.add('hide')

        var loadNewVideo = () => {
          // this event may be called multiple times on some browsers, therefore remove it
          tempVideo.removeEventListener('canplaythrough', loadNewVideo, false)

          let container = document.querySelector('.stimuli-container')
          let prevVideo = document.querySelector('.stimuli-container .stimulus')
          if (prevVideo) {
            let node = document.querySelector('.stimuli-container .stimulus')
            container.removeChild(node)
          }
          container.appendChild(tempVideo)

          window.setTimeout(() => {
            tempVideo.classList.remove('hide')
            this.startTime = new Date()

            if (hideTimer) {
              window.hideTimeout = window.setTimeout(() => {
                tempVideo.classList.add('hide')
              }, hideTimer)
            }
            tempVideo.play()
            // this.focusSelect()
            this.disableNextBtn = false
          }, this.experiment.delay)
        }

        tempVideo.load()
        tempVideo.addEventListener('canplaythrough', loadNewVideo, false)
      } else {
        var tempImage = document.createElement('img')
        tempImage.src = imgLeft.path
        // tempImage.style.width = '100%'
        tempImage.classList.add('stimulus')
        tempImage.classList.add('hide')

        var loadNewImage = () => {
          tempImage.removeEventListener('load', loadNewImage, false)

          let container = document.querySelector('.stimuli-container')
          let prevImage = document.querySelector('.stimuli-container .stimulus')
          if (prevImage) {
            let node = document.querySelector('.stimuli-container .stimulus')
            container.removeChild(node)
          }
          container.appendChild(tempImage)

          window.setTimeout(() => {
            tempImage.classList.remove('hide')
            this.startTime = new Date()

            if (hideTimer) {
              window.hideTimeout = window.setTimeout(() => {
                tempImage.classList.add('hide')
              }, hideTimer)
            }

            // this.focusSelect()
            this.disableNextBtn = false
          }, this.experiment.delay)
        }

        tempImage.addEventListener('load', loadNewImage, false)
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
        this.$refs.images.style.height = height + 'px'
      })
    },

    setKeyboardShortcuts () {
      window.addEventListener('keydown', this.onKeyPress)
    },

    onKeyPress (e) {
      switch (e.code) {
        // case 'ArrowRight':
        // case 'Space':
        case 'Enter':
          if (this.selectedCategory !== null && this.disableNextBtn === false) {
            this.saveAnswer()
          }
          break

        case 'Escape':
          this.abortDialog = true
          break
      }

      // down or up arrow
      // if (e.keyCode === 40 || e.keyCode === 38) {
      //   console.log(this.$refs.select)
      //   // if () {
      //   // this.$refs.select.activateMenu()
      //   // }
      // }
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

      console.log(document.querySelector('.stimuli-container .stimulus').getAttribute('src').split('/').pop())
      console.log(pictureIdLeft.path.split('/').pop())

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
