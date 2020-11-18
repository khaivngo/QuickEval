<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: #' + experiment.background_colour" @keydown.esc="alert('esc')">
    <v-toolbar flat height="30" color="#282828">
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
              <h3 class="subtitle-1 mt-4">1. Images can be moved around. Click and hold down mouse on the image, then move around.</h3>

              <h4 class="subtitle-1 mt-4">2. Go next with either: Space bar / enter key / right arrow key</h4>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="primary darken-1"
                text
                outlined
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
                color="primary darken-1"
                text
                outlined
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
          {{ index2 / 2 }}/{{ (stimuli.length + stimuliIndex) / 2 }}
        </h4>
      </v-toolbar-items>

      <v-toolbar-items>
        <v-dialog v-model="abortDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn text dark color="#D9D9D9" v-on="on">
              Quit
              <!-- <v-icon right small>logout</v-icon> -->
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

    <v-layout mt-3 justify-center>
      <h4 class="subheading font-weight-regular" v-if="experiment.show_original === 1 && originalImage">
        Original
      </h4>
    </v-layout>

    <v-layout ml-3 mr-3 pa-0 style="height: 85vh;" justify-center>
      <v-flex
        mt-2 mb-2
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
        class="picture-container"
      >
        <div class="panzoom">
          <img
            id="picture-left"
            class="picture"
            :class="isLoadLeft === false ? 'hide' : ''"
            :src="leftImage"
            tabindex="0"
          />
        </div>
      </v-flex>

      <v-flex
        mt-2 mb-2
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
        class="picture-container"
        v-if="experiment.show_original === 1"
      >
        <div class="panzoom">
          <img
            id="picture-original"
            class="picture"
            :src="originalImage"
          />
        </div>
      </v-flex>

      <v-flex
        class="picture-container"
        mt-2 mb-2
      >
        <div class="panzoom">
          <img
            id="picture-right"
            class="picture"
            :class="isLoadRight === false ? 'hide' : ''"
            :src="rightImage"
            tabindex="0"
          />
        </div>
      </v-flex>
    </v-layout>

    <v-radio-group v-model="selectedRadio">
      <v-row class="pa-0 ma-0 align-center">
        <v-col class="pa-0 mt-0 pt-1">
          <div class="d-flex justify-center pa-0 mt-0">
            <v-radio color="default" value="left" class="scaled"></v-radio>
          </div>
        </v-col>
        <v-col cols="auto" class="pa-0 mt-0">
          <div class="d-flex justify-center">
            <v-btn
              @click="next()"
              :disabled="selectedRadio === null"
              :loading="disableNextBtn"
              color="#D9D9D9"
            >
              <span class="ml-1">next</span>
              <v-icon>mdi-chevron-right</v-icon>
            </v-btn>
          </div>
        </v-col>
        <v-col class="pa-0 mt-0 pt-1">
          <div class="d-flex justify-center">
            <v-radio color="default" value="right" class="scaled"></v-radio>
          </div>
        </v-col>
      </v-row>
    </v-radio-group>

    <!-- <div class="d-flex justify-center pt-4">
      <v-btn
        @click="next('click')"
        :disabled="selectedRadio === null"
        :loading="disableNextBtn"
        color="#D9D9D9"
      > -->
        <!-- :disabled="noneSelected" -->
        <!-- <span class="ml-1">next</span>
        <v-icon>mdi-chevron-right</v-icon>
      </v-btn>
    </div> -->

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
// import Panzoom from '@panzoom/panzoom'
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'

export default {
  name: 'experiment-view',

  components: {
    FinishedDialog
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
      index2: 0,
      stimuliIndex: 0,
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

      timeElapsed: null,

      firstImages: 1
    }
  },

  // computed: {
  //   // returns true if no image has been selected, or if the "next" button is disabled.
  //   noneSelected () {
  //     return this.disableNextBtn || (this.rightReproductionActive === false && this.leftReproductionActive === false)
  //   }
  // },

  created () {
    this.getExperiment(this.$route.params.id).then(response => {
      this.experiment = response.data

      // checkIfExperimentTaken() -> look for completed key in experimentResults table load index from localstorage or find num rows in results table
      // -> deleteoldresults()

      /* eslint-env jquery */
      $(document).ready(function () {
        (function () {
          // const elem = document.querySelector('.panzoom')
          // if (elem) {
          //   const panzoom = Panzoom(elem, {
          //     maxScale: 1,
          //     minScale: 1
          //   })
          //   elem.addEventListener('panzoomchange', (event) => {
          //     panzoom2.setStyle('transform', `translate(${event.detail.x}px, ${event.detail.y}px)`)
          //   })
          // }

          // const elem2 = document.querySelector('.panzoom2')
          // if (elem2) {
          //   const panzoom2 = elem2(elem, {
          //     maxScale: 1,
          //     minScale: 1
          //   })
          //   elem2.addEventListener('panzoomchange', (event) => {
          //     panzoom.setStyle('transform', `translate(${event.detail.x}px, ${event.detail.y}px)`)
          //   })
          // }

          var $pictureContainer = $('.picture-container')

          $pictureContainer.find('.panzoom').panzoom({
            $set: $pictureContainer.find('.panzoom'), // moves the images in unison
            minScale: 1,
            maxScale: 1,
            $reset: $('.panning-reset')
          }).panzoom('zoom')
        })()
      })

      this.$axios.get(`/experiment/${this.experiment.id}/start`).then((payload) => {
        if (payload) {
          this.stimuli = payload.data

          // count how many instructions do we have
          let count = payload.data.filter((obj) => obj.instruction_id).length
          // let count2 = count * 1 // 3
          // let min = payload.data - count

          // const id = 12
          // const count = array.reduce((acc, cur) => cur.id === id ? ++acc : acc, 0)

          this.stimuliIndex = count

          if (localStorage.getItem(`${this.experiment.id}-index`) === null) {
            localStorage.setItem(`${this.experiment.id}-index`, 0)
          }

          this.index = Number(localStorage.getItem(`${this.experiment.id}-index`))
          this.experimentResult = Number(localStorage.getItem(`${this.experiment.id}-experimentResult`))

          this.next()
        } else {
          alert('Something went wrong. Could not start the experiment.')
        }
      }).catch(err => {
        console.warn(err)
      })
    })

    window.addEventListener('keydown', (e) => {
      if (e.keyCode === 13 || e.keyCode === 39 || e.keyCode === 32) { // enter / arrow right / space
        if (this.selectedRadio !== null) {
          this.next()
        }
      }

      if (e.keyCode === 27) { // esc
        this.abort()
      }
    })
  },

  destroyed () {
    // window.removeEventListener('keydown')
  },

  methods: {
    closeAndNext () {
      this.instructionDialog = false
      this.next()
    },

    /**
     * Load the next image queue stimuli, or instructions.
     */
    next () {
      console.log(this.index)
      // Have we reached the end?
      if (this.stimuli[this.index + 1] === undefined) {
        this.onFinish()
        return
      }

      if (this.stimuli[this.index].hasOwnProperty('picture_queue_id') && this.stimuli[this.index].picture_queue_id !== null) {
        // if (this.timeElapsed === null) this.timeElapsed = new Date()
        let selectedStimuli = null
        if (this.selectedRadio === 'right') selectedStimuli = this.stimuli[this.index]
        if (this.selectedRadio === 'left')  selectedStimuli = this.stimuli[this.index + 1]

        // set original if exists
        if (this.stimuli[this.index].hasOwnProperty('original') && this.stimuli[this.index].hasOwnProperty('original') !== null && this.stimuli[this.index].original && this.stimuli[this.index].original.path) {
          this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].original.path
        }

        // only do stuff if stimuli has been selected
        if (this.selectedRadio !== null) {
          this.disableNextBtn = true

          // record the current time
          let endTime = new Date()
          // subtract the current time with the start time (when images completed loading)
          let timeDiff = endTime - this.timeElapsed // in ms
          // strip the ms and get seconds
          timeDiff /= 1000
          let seconds = Math.round(timeDiff)

          this.store(
            selectedStimuli,
            this.stimuli[this.index],
            this.stimuli[this.index + 1],
            seconds
          ).then(response => {
            if (response.data !== 'result_stored') {
              alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
            }

            this.selectedRadio = null
            this.index += 2
            this.index2 += 2
            localStorage.setItem(`${this.experiment.id}-index`, this.index)

            // Have we reached the end?
            if (this.stimuli[this.index + 1] === undefined) {
              this.onFinish()
              return
            }

            if (this.stimuli[this.index].hasOwnProperty('picture_queue_id') && this.stimuli[this.index].picture_queue_id !== null) {
              this.loadStimuli()
            } else {
              this.disableNextBtn = false

              this.instructionText = this.stimuli[this.index].description
              this.instructionDialog = true

              this.index += 1
              this.index2 += 2
              localStorage.setItem(`${this.experiment.id}-index`, this.index)

              // Have we reached the end?
              if (this.stimuli[this.index + 1] === undefined) {
                this.onFinish()
                // return
              }
              this.loadStimuli() // this could be replaced by resetting this.firstImages = 1
            }
          }).catch(() => {
            this.disableNextBtn = false
            alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
          })
        }

        if (this.firstImages === 1) {
          this.loadStimuli()
          this.firstImages = 2
        }
      } else {
        console.log('instr')
        this.instructionText = this.stimuli[this.index].description
        this.instructionDialog = true

        this.index += 1
        this.index2 += 2
        localStorage.setItem(`${this.experiment.id}-index`, this.index)

        // Have we reached the end?
        if (this.stimuli[this.index + 1] === undefined) {
          this.onFinish()
          // return
        }
      }
    },

    loadStimuli () {
      // prepare to load reproduction images
      var images = [
        { img: new Image(), path: this.$UPLOADS_FOLDER + this.stimuli[this.index].path },
        { img: new Image(), path: this.$UPLOADS_FOLDER + this.stimuli[this.index + 1].path }
      ]
      var imageCount = images.length
      var imagesLoaded = 0
      // attach onload events to every reproduction image
      for (var i = 0; i < imageCount; i++) {
        images[i].img.src = images[i].path
        images[i].img.onload = () => {
          imagesLoaded++
          // when all images loaded
          if (imagesLoaded === imageCount) {
            // hide right image, then set source
            this.isLoadRight = false
            this.rightImage = images[0].img.src
            // hide left image, then set source
            this.isLoadLeft = false
            this.leftImage = images[1].img.src
            // console.log('right - ' + this.rightImage)
            // console.log('left - ' + this.leftImage)

            // show a blank screen inbetween image switching,
            // if scientist set up delay
            window.setTimeout(() => {
              // show left and right image
              this.isLoadLeft = true
              this.isLoadRight = true
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

    async store (pictureIdSelected, pictureIdRight, pictureIdLeft, clientSideTimer) {
      let data = {
        experiment_result_id: this.experimentResult,
        picture_id_selected: pictureIdSelected.picture_id,
        picture_id_right: pictureIdRight.picture_id,
        picture_id_left: pictureIdLeft.picture_id,
        client_side_timer: clientSideTimer,
        chose_none: 0
      }

      return this.$axios.post('/paired-result', data)
    },

    onFinish () {
      this.originalImage = ''
      this.leftImage = ''
      this.rightImage = ''

      // spinner while await
      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      localStorage.removeItem(`${this.experiment.id}-index`)
      localStorage.removeItem(`${this.experiment.id}-experimentResult`)
      this.finished = true
    },

    abort () {
      this.abortDialog = true
      localStorage.removeItem(`${this.experiment.id}-index`)
      localStorage.removeItem(`${this.experiment.id}-experimentResult`)
      this.$router.push('/observer')
    }
  }
}
</script>

<!-- <style>
  #app {
    /*height: 80%;*/
    background: red;
  }
</style> -->

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

.hide {
  opacity: 0;
}

.scaled {
  transform: scale(1.6);
  transform-origin: left;
}

// .parent {
//   overflow: hidden;
//   position: relative;
//   height: 100%;
//   width: 100%;
// }
</style>
