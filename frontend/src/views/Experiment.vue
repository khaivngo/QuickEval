<template>
  <v-container fluid class="qe-wrapper" :style="'background-color:' + bgColour">
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
              <p>If the image disappear outside the dragging area, click "reset panning".</p>
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

    <v-layout mt-3 justify-center>
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
          <img id="pictureLeft" class="picture" src=""/>
        </div>
      </v-flex>

      <v-flex xs4 ma-2 class="picture-container">
        <div class="panzoom">
          <img id="pictureOriginal" class="picture" src=""/>
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

    <v-btn fixed bottom right color="#D9D9D9" @click="next">
      <span class="ml-1">next</span>
      <v-icon>keyboard_arrow_right</v-icon>
    </v-btn>
  </v-container>
</template>

<script>
/* eslint no-use-before-define: 0 */
// import $ from 'jquery'
// import JQuery from 'jquery'
// let $ = JQuery

// import '@/plugins/panzoom/jquery.panzoom.min.js'
// var panzoom = Panzoom

/* eslint no-use-before-define: 2 */

// window.$ = require('jquery')
// window.JQuery = require('jquery')

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

      experiment: {}
    }
  },

  created () {
    /* eslint-env jquery */
    $(document).ready(function () {
      (function () {
        var url = 'https://images4.alphacoders.com/213/thumb-1920-213794.jpg'

        $('.picture').attr('src', url)

        var $pictureContainer = $('.picture-container')

        $pictureContainer.find('.panzoom').panzoom({
          $set: $pictureContainer.find('.panzoom'),
          minScale: 1,
          maxScale: 1,
          $reset: $('.panning-reset')
        }).panzoom('zoom')
      })()
    })

    // fetch experiment data
    this.getExperiment(this.$route.params.id)

    // setOriginal()
    // function setPictureQueue(imagesArray, rightAndLeft, pictureQueueId) {
    // function generateRandomPictureQueue(imagesetId, rightAndLeft) {
    // function postStartData(experimentId) { ... add entry to experiments results table
    //  url: 'ajax/observer/insertExperimentResultData.php',
    // async: false,

    // data: {
    //     'browser': browser.name + "(" + browser.version + ")",
    //     'os': getOs(),
    //     'xDimension': dimension['width'],
    //     'yDimension': dimension['height'],
    //     'startTime': getDateTime(),
    //     'experimentId': experimentId,
    // },
    // function checkIfExperimentTaken() {
    // deleteoldresults
    this.$axios.post('/queue/store')
      .then(payload => console.log(payload))
      .catch(err => console.warn(err))
  },

  methods: {
    setOriginal () {
      // url: 'ajax/observer/getShowOriginal.php',
      // success: function (data) {
      //     if (data[0].showOriginal == 0) {
      //         removeOriginal()
      //         this.original = false
      //     }
      // }
    },

    async getExperiment (experimentId) {
      return this.$axios.get('/experiment/' + experimentId)
        .then(payload => (this.experiment = payload.data))
        .catch(err => console.warn(err))
    },

    abort () {
      this.abortDialog = true
      this.$router.push('/observer')
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

    next () {
      // if () {
      //   return
      // }
      // function postResults(type, pictureOrderId, choose)

      this.rightReproductionActive = false
      this.leftReproductionActive = false

      // remove, and load new images
      var url = 'https://cakebycourtney.com/wp-content/uploads/2016/04/Tonight-Show-Cake-3-1024x683.jpg'
      $('.picture').attr('src', url)

      $('.picture-container').find('.panzoom').panzoom('reset', { animate: false })

      // $('.picture-container').panzoom("resetPan", {
      //   animate: false,
      //   silent: true
      // })

      //
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
