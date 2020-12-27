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
                color="primary darken-1"
                text
                @click="closeInstructions"
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
      <v-flex ml-2 mr-2 xs6 v-if="experiment.show_original === 1" justify-center align-center>
      </v-flex>
    </v-layout>

    <v-layout ref="titles" pa-0 pt-4 ma-0 justify-center align-center>
      <v-flex ml-2 mr-2 xs6 class="justify-center" justify-center align-center>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 class="text-center">
        <h4 class="subtitle-1 pb-0 mb-0" v-if="experiment.show_original === 1 && originalImage">
          Original
        </h4>
      </v-flex>
    </v-layout>

    <v-layout ref="images" fill-height justify-center ml-3 mr-3 pa-0 pt-2>
      <v-flex :style="(experiment.show_original) ? `margin-right: ${experiment.stimuli_spacing}px` : ''" mt-0 mr-2 mb-0 pb-2 class="picture-container">
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

      <v-flex mt-0 ml-2 mb-0 pb-2 class="picture-container" v-if="experiment.show_original === 1">
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
            <!-- <h4 class="ml-1 mr-1">
              ({{ index }}/{{ totalComparisons }})
            </h4> -->

            <!-- <h4 class="ml-4" style="color: #BDBDBD;">
              {{ index }}/{{ totalComparisons }}
            </h4> -->
            <h4 class="ml-1">
              ({{ index }}/{{ totalComparisons }})
            </h4>
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </v-layout>
      </v-flex>

      <v-flex ml-2 mr-2 xs6 v-if="experiment.show_original === 1" class="justify-center" justify-center align-center>
      </v-flex>
    </v-layout>

    <!-- <div style="position: fixed; bottom: 2%; left: 1.4%; right: 50.9%;">
      <v-pagination
        :length="6"
      ></v-pagination>
      <v-tabs color="rgb(195, 195, 195)" show-arrows style="border-radius: 2px;">
        <v-tabs-slider color="#000"></v-tabs-slider>
        <v-tab
          v-for="(item, i) in ['good', 'best', 'very long criteria', 'unsure', 'I\'ve seen better', 'another critera with long text', 'excellent', 'terrible', 'terrific', 'perfect']"
          :key="i"
          :href="'#tab-' + i"
        >
          <span style="color: #000;">{{ item }}</span>
        </v-tab>
      </v-tabs>
    </div> -->

    <!-- <v-btn fixed bottom right color="#D9D9D9"
      @click="next()"
      :disabled="disableNextBtn || (selectedCategory === null)"
      :loading="disableNextBtn"
    >
      <span class="ml-1">next</span>
      <v-icon>mdi-chevron-right</v-icon>
    </v-btn> -->

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'
import ArtifactMarkerToolbar from '@/components/ArtifactMarkerToolbar'
import ArtifactMarker from '@/components/ArtifactMarker'
import { datetimeToSeconds } from '@/functions/datetimeToSeconds.js'

export default {
  name: 'category-experiment-view',

  components: {
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
      rightImage: '',

      startTime: null,

      firstImages: 1,

      // artifact marking
      leftCanvas: '',
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

      this.$axios.get(`/experiment/${this.experiment.id}/start`).then((payload) => {
        if (payload) {
          this.stimuli = payload.data

          var total2 = 0
          this.experiment.sequences.forEach((sequence) => {
            if (sequence.hasOwnProperty('picture_queue')) {
              total2 += Number(sequence.picture_queue.picture_sequence_count)
            }
          })
          // console.log(total2)
          this.totalComparisons = total2

          // this.totalComparisons = this.experiment.sequences.reduce((a, b) => a + b.picture_queue.picture_sequence_count, 0)

          if (localStorage.getItem(`${this.experiment.id}-index`) === null) {
            localStorage.setItem(`${this.experiment.id}-index`, 0)
          }

          this.index = Number(localStorage.getItem(`${this.experiment.id}-index`))
          this.experimentResult = Number(localStorage.getItem(`${this.experiment.id}-experimentResult`))

          this.next()

          this.$nextTick(() => {
            let navMain = 30
            let navMarker = this.$refs.navMarker.offsetHeight
            let titles = this.$refs.titles.offsetHeight
            let navAction = this.$refs.navAction.offsetHeight
            let minus = navMain + titles + navAction + navMarker

            var height = document.body.scrollHeight - minus - 20
            this.$refs.images.style.maxHeight = height + 'px'
          })
        } else {
          alert('Something went wrong. Could not start the experiment.')
        }
      }).catch(err => {
        console.warn(err)
      })

      window.addEventListener('keydown', (e) => {
        if (e.keyCode === 13 || e.keyCode === 39 || e.keyCode === 32) { // enter / arrow right / space
          if (this.selectedCategory !== null) {
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

    closeInstructions () {
      this.instructionDialog = false
      this.focusSelect()
    },

    focusSelect () {
      window.setTimeout(() => {
        this.$refs.select.$el.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].focus()
      }, 400)
    },

    drawn (shapes) {
      // shapes.uuid let's us distinguish between left and right image canvas
      this.shapes[shapes.uuid] = shapes.shapes
    },

    changedDrawingTool (string) {
      this.drawingTool = string
    },

    /**
     * Load the next image stimuli queue, or instructions.
     */
    next () {
      // Have we reached the end?
      if (this.stimuli[this.index] === undefined) {
        this.onFinish()
        return
      }

      this.focusSelect()

      if (this.stimuli[this.index].hasOwnProperty('picture_queue_id') && this.stimuli[this.index].picture_queue_id !== null) {
        // set original
        if (
          this.stimuli[this.index].hasOwnProperty('original') &&
          this.stimuli[this.index].hasOwnProperty('original') !== null &&
          this.stimuli[this.index].original &&
          this.stimuli[this.index].original.path
        ) {
          this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].original.path
        }

        // this.leftImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].path

        // don't do anything unless category has been selected
        if (this.selectedCategory !== null) {
          this.disableNextBtn = true

          // record the current time
          let endTime = new Date()
          // get the number of seconds between endTime and startTime
          let seconds = datetimeToSeconds(this.startTime, endTime)

          this.store(this.stimuli[this.index], seconds).then(response => {
            if (response.data !== 'result_stored') {
              alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
            }

            this.selectedCategory = null
            this.index += 1
            localStorage.setItem(`${this.experiment.id}-index`, this.index)

            // Have we reached the end?
            if (this.stimuli[this.index] === undefined) {
              this.onFinish()
              return
            }

            this.loadStimuli()

            this.focusSelect()
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
        this.instructionText = this.stimuli[this.index].description
        this.instructionDialog = true

        this.index += 1
        localStorage.setItem(`${this.experiment.id}-index`, this.index)

        this.next()
      }
    },

    loadStimuli () {
      var imgLeft = new Image()
      imgLeft.src = this.$UPLOADS_FOLDER + this.stimuli[this.index].path
      imgLeft.onload = () => {
        this.isLoadLeft = false
        this.leftImage = imgLeft.src
        this.leftCanvas =  { path: imgLeft.src, image: this.stimuli[this.index] }
        window.setTimeout(() => {
          this.isLoadLeft = true
          this.startTime = new Date()
          // console.log(this.leftImage)
          this.disableNextBtn = false
        }, this.experiment.delay)
      }
    },

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async store (pictureIdLeft, clientSideTimer) {
      let data = {
        experiment_result_id: this.experimentResult,
        category_id: this.selectedCategory,
        picture_id_left: pictureIdLeft.picture_id,
        client_side_timer: clientSideTimer,
        chose_none: 0
      }

      return this.$axios.post('/category-result', data)
    },

    onFinish () {
      this.originalImage = ''
      this.leftImage = ''
      this.rightImage = ''
      this.disableNextBtn = false

      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      localStorage.removeItem(`${this.experiment.id}-index`)
      localStorage.removeItem(`${this.experiment.id}-experimentResult`)
      this.finished = true
    },

    abort () {
      localStorage.removeItem(`${this.experiment.id}-index`)
      localStorage.removeItem(`${this.experiment.id}-experimentResult`)
      this.abortDialog = true
      this.$router.push('/observer')
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
    // color: #fff;
  }
  .theme--light.v-list {
    background: #bbb;
    // color: #fff;
  }
  .theme--light.v-list.v-list-item__content.v-list-item__title {
    color: #fff;
  }
  // .v-menu__content.theme--light {
  //   max-height: 800px;
  // }
  // div.v-list.v-select-list {
  //   display: flex;
  // }
  // v-sheet theme--light theme--light
  // .theme--light.v-list-item:hover:before {
  //   opacity: 0.4;
  // }
</style>
