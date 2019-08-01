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

    <v-btn fixed bottom right color="#D9D9D9" @click="next(23)" :disabled="disableNextBtn">
      <span class="ml-1">next</span>
      <v-icon>keyboard_arrow_right</v-icon>
    </v-btn>
  </v-container>
</template>

<script>
export default {
  data () {
    return {
      instructionsDialog: false,
      abortDialog: false,

      bgColour: '#808080',
      distance: 20,
      instructionsText: 'Rate the images.',

      rightReproductionActive: false,
      leftReproductionActive: false,

      experiment: {
        id: null
      },

      original: true,

      disableNextBtn: false
    }
  },

  created () {
    this.getExperiment(this.$route.params.id).then(response => {
      this.experiment = response.data

      // checkIfExperimentTaken() -> load index from localstorage or find num rows in results table
      // -> deleteoldresults()

      /* eslint-env jquery */
      $(document).ready(function () {
        (function () {
          var url = 'https://images4.alphacoders.com/213/thumb-1920-213794.jpg'

          $('#picture-left').attr('src', url)
          $('#picture-original').attr('src', url)
          $('#picture-right').attr('src', url)

          var $pictureContainer = $('.picture-container')

          $pictureContainer.find('.panzoom').panzoom({
            $set: $pictureContainer.find('.panzoom'),
            minScale: 1,
            maxScale: 1,
            $reset: $('.panning-reset')
          }).panzoom('zoom')
        })()
      })

      this.$axios.get('/experiment/' + this.experiment.id + '/start').then(payload => {
        if (payload) {
          // this.setOriginal()
        }
        console.log(payload)
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

    next (pictureOrderId) {
      // exit if no stimuli has been selected
      if (this.rightReproductionActive === false && this.leftReproductionActive === false) {
        return
      }

      this.disableNextBtn = true

      // next index in array
      // if image
      // if instruction

      // save sequence/queueIndex localStorage

      this.store(pictureOrderId, null, 0).then(response => {
        if (response.data === 'result_stored') {
          // if (this.sequence, last step)
          // this.nextSequence().then(response => {
          //   if (response.data) {
          //     console.log(response.data)

          //     this.rightReproductionActive = false
          //     this.leftReproductionActive = false

          //     this.loadImages()
          //     this.disableNextBtn = false
          //   } else {
          //     alert('Could not load next sequence.')
          //     this.disableNextBtn = false
          //   }
          // })
        } else {
          alert('Could not save your answer.')
          this.disableNextBtn = false
        }
      })

      // $('.picture-container').panzoom("resetPan", {
      //   animate: false,
      //   silent: true
      // })

      //
    },

    onFinish () {
      // delete loalStorage
    },

    loadImages () {
      // remove, and load new images
      var url = 'https://cakebycourtney.com/wp-content/uploads/2016/04/Tonight-Show-Cake-3-1024x683.jpg'

      $('#picture-left').attr('src', url)
      $('#picture-original').attr('src', url)
      $('#picture-right').attr('src', url)

      $('.picture-container').find('.panzoom').panzoom('reset', { animate: false })
    },

    async nextSequence () {
      return this.$axios.get('/')
        .catch(err => console.warn(err))
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

    setOriginal () {
      //
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
