<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: ' + bgColour">
    <v-toolbar flat height="50" color="#282828">
      <v-toolbar-items>
        <v-dialog v-model="instructionsDialog" max-width="500">
          <template v-slot:activator="{ on }">
            <v-btn flat dark color="#D9D9D9" v-on="on">
              Instructions
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="headline">Instructions</v-card-title>
            <v-card-text>
              <div class="body-2">{{ instructionsText }}</div>

              <h4 class="mt-5">Note</h4>
              <p>If the images disappear outside the dragging area, click "reset panning".</p>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="green darken-1" flat @click="instructionsDialog = false">Close</v-btn>
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
              Quit Experiment
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

    <v-layout mt-3 justify-center v-if="original">
      <h4 class="subheading font-weight-regular">Original</h4>
    </v-layout>

    <v-layout ml-3 mr-3 pa-0 style="height: 85vh;">
      <v-flex
        class="picture-container"
        :class="leftReproductionActive ? 'selected' : ''"
        @click="toggleSelected('left')"
        xs4 ma-2
      >
        <div class="panzoom">
          <img id="picture-left" class="picture" src=""/>
        </div>
      </v-flex>

      <v-flex xs4 ma-2 class="picture-container" v-if="original">
        <div class="panzoom">
          <img id="picture-original" class="picture" src=""/>
        </div>
      </v-flex>

      <v-flex
        class="picture-container"
        :class="rightReproductionActive ? 'selected' : ''"
        @click="toggleSelected('right')"
        xs4 ma-2
      >
        <div class="panzoom">
          <img id="picture-right" class="picture" src=""/>
        </div>
      </v-flex>
    </v-layout>

    <v-btn fixed bottom right color="#D9D9D9" @click="next()" :disabled="disableNextBtn">
      <span class="ml-1">next</span>
      <v-icon>keyboard_arrow_right</v-icon>
    </v-btn>

    <v-dialog persistent v-model="iDialog" max-width="500">
      <v-card>
        <v-card-title class="headline">
          Instructions
        </v-card-title>

        <v-card-text>
          {{ instructionText }}
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green darken-1"
            flat="flat"
            @click="iDialog = false"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  name: 'experiment-view',

  data () {
    return {
      bgColour: '#808080',
      distance: 20,
      instructionsText: 'Rate the images.',

      experiment: {
        id: null,
        show_original: null
      },

      stimuli: [],

      original: true,
      index: 0,

      selectedStimuli: null,

      rightReproductionActive: false,
      leftReproductionActive: false,
      disableNextBtn: false,

      instructionsDialog: false,
      abortDialog: false,

      iDialog: false,
      instructionText: '',

      leftImage: ''
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
          var url = 'https://images4.alphacoders.com/213/thumb-1920-213794.jpg'

          // $('#picture-left').attr('src', url)
          $('#picture-original').attr('src', url)
          // $('#picture-right').attr('src', url)

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
          console.log(this.index)
          this.next()
        } else {
          alert('Something went wrong. Could not start the experiment.')
        }
      }).catch(err => {
        console.warn(err)
      })
    })
  },

  methods: {
    toggleSelected (side) {
      if (side === 'left') {
        this.leftReproductionActive = !this.leftReproductionActive
        this.rightReproductionActive = false
      } else {
        this.rightReproductionActive = !this.rightReproductionActive
        this.leftReproductionActive = false
      }
    },

    /**
     * Load the next image queue stimuli, or instructions.
     */
    next () {
      this.disableNextBtn = true

      if (this.stimuli[this.index].hasOwnProperty('picture_queue_id') && this.stimuli[this.index].picture_queue_id !== null) {
        // if right-hand reproduction is selected, get the stimuli object from the array of stimuli
        let selectedStimuli = (this.rightReproductionActive === true) ? this.stimuli[this.index] : this.stimuli[this.index + 1]

        // exit if no stimuli has been selected
        // if (this.rightReproductionActive === false && this.leftReproductionActive === false) {
        //   return
        // }

        this.store(selectedStimuli.id, null, 0).then(response => {
          this.disableNextBtn = false

          if (response.data === 'result_stored') {
            // have we reached the end?
            // if (this.index === this.stimuli.length - 1) {
            //   // display dialog, redirect on close
            //   this.disableNextBtn = false
            //   return
            // }

            // this.setOriginal() THIS NEEDS TO CHECK/CHANGE EVERY PICTURE QUEUE...
            this.getImage(this.stimuli[this.index].picture_id).then(image => {
              $('#picture-left').attr('src', this.$UPLOADS_FOLDER + image.data.path)
              // this.leftImage = image.data.path
            })

            this.getImage(this.stimuli[this.index + 1].picture_id).then(image => {
              $('#picture-right').attr('src', this.$UPLOADS_FOLDER + image.data.path)
            })

            // this.loadImages(leftImage, rightImage, null)

            // console.log(this.stimuli[this.index].picture_id)
            // console.log(this.stimuli[this.index + 1].picture_id)

            selectedStimuli = null
            this.rightReproductionActive = false
            this.leftReproductionActive = false
            this.index += 2
            // localStorage.setItem('index', this.index)
          } else {
            alert('Could not save your answer. Please try again. If the problems consists please contact the researcher.')
          }
        })

        this.disableNextBtn = false
      } else {
        this.iDialog = true
        this.instructionText = this.stimuli[this.index].description
        this.index += 1
        // localStorage.setItem('index', this.index)
        this.disableNextBtn = false
        // IMPROVEMENT: show instruction, on close go next
        this.next()
      }
    },

    async getImage (id) {
      return this.$axios.get('/picture/' + id)
        .catch(err => console.warn(err))
    },

    async getOriginal () {
      // return this.$axios.get('/experiment/' + experimentId)
      //   .catch(err => console.warn(err))
    },

    loadImages (urlLeft, urlRight, urlOriginal) {
      $('#picture-left').attr('src', urlLeft)
      $('#picture-right').attr('src', urlRight)

      if (urlOriginal !== null) {
        $('#picture-original').attr('src', urlOriginal)
      }

      $('.picture-container').find('.panzoom')
        .panzoom('reset', {
          animate: false
        })
    },

    setOriginal (url) {
      $('#picture-original').attr('src', url)
    },

    onFinish () {
      // delete loalStorage
    },

    async store (pictureOrderId, categoryId, choseNone) {
      let data = {
        experiment_id: this.experiment.id,
        picture_order_id: pictureOrderId,
        category_id: categoryId,
        chose_none: choseNone
      }

      return this.$axios.post('/result', data)
        .catch(err => console.warn(err))
    },

    async getExperiment (experimentId) {
      return this.$axios.get('/experiment/' + experimentId)
        .catch(err => console.warn(err))
    },

    abort () {
      this.abortDialog = true
      this.$router.push('/observer')

      // delete localStorage!
    }
  }
}
</script>

<style scoped lang="scss">
// @import '~vuetify/src/stylus/theme.styl'

// $material-light.background = #808080

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
  // opacity: 0.3;
}

// .parent {
//   overflow: hidden;
//   position: relative;
//   height: 100%;
//   width: 100%;
// }
</style>
