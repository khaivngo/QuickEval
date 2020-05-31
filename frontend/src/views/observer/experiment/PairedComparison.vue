<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: #' + experiment.background_colour" @keydown.esc="alert('esc')">
    <v-toolbar flat height="30" color="#282828">
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
              Quit
              <!-- <v-icon right small>logout</v-icon> -->
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

    <v-layout mt-3 justify-center>
      <h4 class="subheading font-weight-regular" v-if="experiment.show_original === 1">
        Original
      </h4>
    </v-layout>

    <v-layout ml-3 mr-3 pa-0 style="height: 85vh;" justify-center>
      <v-flex
        mt-2 mb-2
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
        class="picture-container"
        :class="leftReproductionActive ? 'selected' : ''"
        @click="toggleSelected('left')"
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
        :class="rightReproductionActive ? 'selected' : ''"
        @click="toggleSelected('right')"
        mt-2 mb-2
      >
        <div class="panzoom">
          <img
            id="picture-right"
            class="picture"
            :class="isLoadRight === false ? 'hide' : ''"
            :src="rightImage"
            tabindex="0"
            @focus="toggleSelected('right')"
          />
        </div>
      </v-flex>
    </v-layout>

    <v-btn
      @click="next('click')"
      :disabled="noneSelected"
      :loading="disableNextBtn"
      color="#D9D9D9"
      fixed
      bottom
      right
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

      stimuli: [],

      index: 0,
      experimentResult: null,

      isLoadLeft: false,
      isLoadRight: false,

      selectedStimuli: null,

      rightReproductionActive: false,
      leftReproductionActive: false,
      disableNextBtn: false,

      instructionText: 'Rate the images.',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      leftImage: '',
      rightImage: '',

      timeElapsed: null
    }
  },

  computed: {
    // returns true if no image has been selected, or if the "next" button is disabled.
    noneSelected () {
      return this.disableNextBtn || (this.rightReproductionActive === false && this.leftReproductionActive === false)
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

    window.addEventListener('keydown', (e) => {
      // esc
      if (e.keyCode === 27) {
        this.abort()
      }
      // arrow right
      if (e.keyCode === 39) {
        if (this.rightReproductionActive !== false || this.leftReproductionActive !== false) {
          this.next()
        }
      }
    })
  },

  destroyed () {
    // window.removeEventListener('keydown')
  },

  methods: {
    /**
     * Load the next image queue stimuli, or instructions.
     */
    next () {
      // Have we reached the end?
      if (this.stimuli[this.index + 1] === undefined) {
        this.onFinish()
        return
      }

      if (this.stimuli[this.index].hasOwnProperty('picture_queue_id') && this.stimuli[this.index].picture_queue_id !== null) {
        // if (this.timeElapsed === null) this.timeElapsed = new Date()
        let selectedStimuli = 0
        if (this.rightReproductionActive === true) selectedStimuli = this.stimuli[this.index]
        if (this.leftReproductionActive === true)  selectedStimuli = this.stimuli[this.index + 1]

        // set original
        if (this.stimuli[this.index].hasOwnProperty('original')) {
          this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].original.path
        }

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

              // show a blank screen inbetween image switching,
              // if scientist set up delay
              window.setTimeout(() => {
                // show left and right image
                this.isLoadLeft = true
                this.isLoadRight = true
                // starts or overrides existing timer
                this.timeElapsed = new Date()
              }, this.experiment.delay)
            }
          }
        }

        // only do stuff if stimuli has been selected
        if (this.rightReproductionActive !== false || this.leftReproductionActive !== false) {
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

            this.disableNextBtn = false
            this.rightReproductionActive = false
            this.leftReproductionActive = false
            this.index += 2
            localStorage.setItem('index', this.index)

            // Have we reached the end?
            if (this.stimuli[this.index + 1] === undefined) {
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

    toggleSelected (side) {
      if (side === 'left') {
        this.leftReproductionActive = !this.leftReproductionActive
        this.rightReproductionActive = false
      } else {
        this.rightReproductionActive = !this.rightReproductionActive
        this.leftReproductionActive = false
      }
    },

    async store (pictureIdSelected, pictureIdLeft, pictureIdRight, clientSideTimer) {
      let data = {
        experiment_result_id: this.experimentResult,
        picture_id_selected: pictureIdSelected.picture_id,
        picture_id_left: pictureIdLeft.picture_id,
        picture_id_right: pictureIdRight.picture_id,
        client_side_timer: clientSideTimer,
        chose_none: 0
      }

      return this.$axios.post('/paired-result', data)
    },

    onFinish () {
      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      localStorage.removeItem('index')
      localStorage.removeItem('experimentResult')
      this.finished = true
    },

    abort () {
      this.abortDialog = true
      localStorage.removeItem('index')
      localStorage.removeItem('experimentResult')
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

// .parent {
//   overflow: hidden;
//   position: relative;
//   height: 100%;
//   width: 100%;
// }
</style>
