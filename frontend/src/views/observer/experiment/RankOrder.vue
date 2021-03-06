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
                @click="instructionDialog = false"
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
          {{ index }}/{{ totalComparison }}
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
              <v-btn color="default darken-1" text @click="abortDialog = false">Continue</v-btn>
              <v-btn color="red darken-1" text @click="abort">Quit</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar-items>
    </v-toolbar>

    <v-layout ref="images" fill-height ml-3 mt-0 mb-0 mr-3 pa-0 pt-2 justify-center>
      <v-flex class="picture-container" mt-2 mb-1 ml-2 :style="'margin-right:' + experiment.stimuli_spacing + 'px'">
        <div class="panzoom d-flex justify-center align-center">
          <img
            id="picture-left"
            class="picture"
            :class="isLoadLeft === false ? 'hide' : ''"
            @load="loadedLeft"
            :src="leftImage"
          />
        </div>
      </v-flex>

      <v-flex
        class="picture-container"
        mt-2 mb-1
        v-if="experiment.show_original === 1"
        :style="'margin-right:' + experiment.stimuli_spacing + 'px'"
      >
        <div class="panzoom d-flex justify-center align-center">
          <img
            id="picture-original"
            class="picture"
            :src="originalImage"
          />
        </div>
      </v-flex>

      <v-flex class="picture-container" mt-2 mb-1 mr-2>
        <div class="panzoom d-flex justify-center align-center">
          <img
            id="picture-right"
            class="picture"
            :class="isLoadRight === false ? 'hide' : ''"
            @load="loadedRight"
            :src="rightImage"
          />
        </div>
      </v-flex>
    </v-layout>

    <v-layout ref="titles" pb-2>
      <v-layout justify-center class="ml-2 mr-2">
        <div
          v-for="(label, i) in labels"
          :key="label"
          @click="changeLeftPannerImage(label)"
          class="letter subheading pt-1 pb-1 pl-2 pr-2 ma-1"
          :class="(label === activeLeft) ? 'active' : ''"
          tabindex="0"
          @keyup.enter="changeLeftPannerImage(label)"
        >
          {{ alphabet[i].toUpperCase() }}
        </div>
      </v-layout>

      <v-layout pa-0 ma-0 justify-center align-center class="text-center ml-2 mr-2">
        <h4 class="subtitle-1 pt-3" v-if="experiment.show_original === 1">
          Original
        </h4>
      </v-layout>

      <v-layout justify-center class="ml-2 mr-2">
        <div
          v-for="(label, i) in labels"
          :key="label"
          @click="changeRightPannerImage(label)"
          class="letter subheading pt-1 pb-1 pl-2 pr-2 ma-1"
          :class="(label === activeRight) ? 'active' : ''"
          tabindex="0"
          @keyup.enter="changeRightPannerImage(label)"
        >
          {{ alphabet[i].toUpperCase() }}
        </div>
      </v-layout>
    </v-layout>

    <div ref="navAction" class="rating pt-3">
      <v-layout justify-center align-center class="pt-2 pa-0">
        <template v-if="rankings">
          <div class="text-center subheading" v-for="(num, i) in rankings.length" :key="i"
            style="width: 50px; margin-left: 3px; margin-right: 3px; margin-top: 3px;"
          >
            <!-- style="width: 100px; margin-left: 3px; margin-right: 3px; margin-top: 3px;" -->
            #{{ num }}
          </div>
        </template>
      </v-layout>

      <v-layout justify-center align-center class="mt-1 pa-0">
        <draggable
          :list="rankings"
          ghost-class="ghost"
          class="list-group"
          v-bind="dragOptions"
          @start="beingDragged = true"
          @end="dragEnd"
        >
          <transition-group type="transition" :name="!beingDragged ? 'flip-list' : null" style="display: flex;">
            <div v-for="element in rankings" :key="element.id" class="moveable-image">
              <!-- <div class="draggable-title headline"> -->
              <div class="draggable-title headline" style="background: none;">
                {{ element.letter }}
              </div>
              <div style="width: 50px; height: 50px; display: block; background: rgba(0,0,0,0.4);"></div>
              <!-- <img style="width: 100px; display: block;" :src="`${$UPLOADS_FOLDER}${element.path}`"/> -->
            </div>
          </transition-group>
        </draggable>
      </v-layout>
    </div>

    <v-btn
      @click="next('click')"
      :loading="disableNextBtn"
      fixed bottom right
      color="#D9D9D9"
    >
      <span class="ml-1">next</span>
      <v-icon>mdi-chevron-right</v-icon>
    </v-btn>

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'
import draggable from 'vuedraggable'
import { datetimeToSeconds } from '@/functions/datetimeToSeconds.js'

const alphabet = 'abcdefghijklmnopqrstuvwxyz'.split('')

export default {
  name: 'experiment-view',

  components: {
    FinishedDialog,
    draggable
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
      currentStimuli: [],

      index: 0,
      experimentResult: null,

      alphabet: alphabet,
      labels: [],
      rankings: [],
      beingDragged: false,
      activeLeft: null,
      activeRight: null,
      isLoadLeft: false,
      isLoadRight: false,

      disableNextBtn: false,

      instructionText: '',

      abortDialog: false,
      instructionDialog: false,
      finished: false,

      originalImage: '',
      leftImage: '',
      rightImage: '',

      timeElapsed: null,

      // firstRound: 1

      totalComparison: 0
    }
  },

  computed: {
    dragOptions () {
      return {
        animation: 200,
        group: 'description',
        disabled: false,
        ghostClass: 'ghost'
      }
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

      const exists = Number(localStorage.getItem(`${this.experiment.id}-index`))
      // if localStorage does not exists for this experiment
      if (exists === null || exists === 0) {
        this.startNewExperiment()
      } else {
        this.continueExistingExperiment()
      }
    })
  },

  methods: {
    datetimeToSeconds: datetimeToSeconds,

    dragEnd (e) {
      this.beingDragged = false
      // this.rankings[e.newIndex].moved = true
      // e.moved.element.moved = true
    },

    changeLeftPannerImage (label) {
      this.isLoadLeft = false

      let elem = this.rankings.find(e => e.id === label)
      this.$nextTick(() => {
        this.leftImage = this.$UPLOADS_FOLDER + elem.path
      })

      this.activeLeft = label
      // tag the stimuli as watched
      elem.watched = true
    },

    loadedLeft () {
      window.setTimeout(() => {
        this.isLoadLeft = true
        // starts or overrides existing timer
        this.timeElapsed = new Date()
        this.disableNextBtn = false
      }, this.experiment.delay)
    },

    changeRightPannerImage (label) {
      this.isLoadRight = false

      let elem = this.rankings.find(e => e.id === label)
      this.$nextTick(() => {
        this.rightImage = this.$UPLOADS_FOLDER + elem.path
      })

      this.activeRight = label
      // tag the stimuli as watched
      elem.watched = true
    },

    loadedRight () {
      window.setTimeout(() => {
        this.isLoadRight = true
        this.timeElapsed = new Date()
        this.disableNextBtn = false
      }, this.experiment.delay)
    },

    continueExistingExperiment () {
      // fetch the existing progress from localStorage
      this.stimuli = JSON.parse(localStorage.getItem(`${this.experiment.id}-stimuliQueue`))
      this.index = Number(localStorage.getItem(`${this.experiment.id}-index`))
      this.experimentResult = Number(localStorage.getItem(`${this.experiment.id}-experimentResult`))

      const amount = this.stimuli.filter(item => item.hasOwnProperty('picture_queue'))
      this.totalComparison = amount.length

      this.next()

      this.$nextTick(() => {
        let navMain = 30
        // let navMarker = this.$refs.navMarker.offsetHeight
        let titles = this.$refs.titles.offsetHeight
        // let navAction = this.$refs.navAction.offsetHeight
        let navAction = 159
        let minus = navMain + titles + navAction // + navMarker
        console.log(document.body.scrollHeight)
        var height = document.body.scrollHeight - minus - 20
        this.$refs.images.style.maxHeight = height + 'px'
      })
    },

    startNewExperiment () {
      this.$axios.get(`/experiment/${this.experiment.id}/start`).then((payload) => {
        this.stimuli = payload.data

        const stimuliQueue = JSON.stringify(this.stimuli)
        localStorage.setItem(`${this.experiment.id}-stimuliQueue`, stimuliQueue)
        localStorage.setItem(`${this.experiment.id}-index`, 0)

        this.index = Number(localStorage.getItem(`${this.experiment.id}-index`))
        this.experimentResult = Number(localStorage.getItem(`${this.experiment.id}-experimentResult`))

        const amount = this.stimuli.filter(item => item.hasOwnProperty('picture_queue'))
        this.totalComparison = amount.length

        this.next()

        this.$nextTick(() => {
          let navMain = 30
          // let navMarker = this.$refs.navMarker.offsetHeight
          let titles = this.$refs.titles.offsetHeight
          // let navAction = this.$refs.navAction.offsetHeight
          let navAction = 159
          let minus = navMain + titles + navAction // + navMarker
          var height = document.body.scrollHeight - minus - 20
          this.$refs.images.style.maxHeight = height + 'px'
        })
      }).catch((err) => {
        window.alert(err)
      })
    },

    /**
     * Load the next image set stimuli, or instructions.
     */
    next (action) {
      // Have we reached the end?
      if (this.stimuli[this.index] === undefined) {
        this.onFinish()
        return
      }

      if (this.stimuli[this.index].hasOwnProperty('picture_queue') && this.stimuli[this.index].picture_queue[0].picture_queue_id !== null) {
        // set the new images
        this.changeStimuli()

        // if this is the first set we want to load the images even though not everything has been watched
        // var firstRound = 1
        // if (this.firstRound === 1) {
        //   this.index += 1
        //   localStorage.setItem('index', this.index)
        //   this.firstRound = 0
        // }

        // has every image been viewed in the panner?
        let watched = this.rankings.filter(element => element.hasOwnProperty('watched'))

        // if rankings array is not empty, and every item in the array has been opened in the panner
        if ((this.rankings.length !== 0) && (watched.length === this.rankings.length)) {
          this.disableNextBtn = true

          // record the current time
          let endTime = new Date()
          // get the number of seconds between endTime and startTime
          let seconds = datetimeToSeconds(this.timeElapsed, endTime)

          this.store(seconds).then(response => {
            this.labels = []
            this.rankings = []

            this.index += 1
            localStorage.setItem(`${this.experiment.id}-index`, this.index)

            // Have we reached the end?
            if (this.stimuli[this.index] === undefined) {
              this.onFinish()
            }

            this.next()
          }).catch(() => {
            this.disableNextBtn = false
            // alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
            // console.log(error)
          })
        }
      } else {
        this.instructionText = this.stimuli[this.index].instructions[0].description
        this.instructionDialog = true

        this.index += 1
        localStorage.setItem(`${this.experiment.id}-index`, this.index)

        this.next()
      }
    },

    /**
     * Set stimuli images and labels, based on current index.
     */
    changeStimuli () {
      this.rankings = this.stimuli[this.index].picture_queue
      // put the two first images in the left and right panner
      this.activeLeft  = this.stimuli[this.index].picture_queue[0].id
      this.activeRight = this.stimuli[this.index].picture_queue[1].id
      // mark the two first images as watched
      this.rankings[0].watched = true
      this.rankings[1].watched = true

      this.labels = []
      // give each image a letter ID, and create labels
      this.rankings.forEach((item, i) => {
        this.$set(item, 'letter', this.alphabet[i].toUpperCase())
        this.labels.push(item.id)
      })

      // set original
      if (
        // this.stimuli[this.index].picture_queue[0].hasOwnProperty('original') &&
        // this.stimuli[this.index].picture_queue[0].hasOwnProperty('original') !== null &&
        // this.stimuli[this.index].picture_queue[0].hasOwnProperty('original')
        this.stimuli[this.index].picture_queue[0].original &&
        this.stimuli[this.index].picture_queue[0].original.path
      ) {
        this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].picture_queue[0].original.path
      } else {
        this.originalImage = ''
      }
      this.leftImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].picture_queue[0].path
      this.rightImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].picture_queue[1].path
    },

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async store (clientSideTimer) {
      let data = {
        experiment_result_id: this.experimentResult,
        rankings: this.rankings,
        client_side_timer: clientSideTimer
      }

      return this.$axios.post('/rank-order-result', data)
    },

    onFinish () {
      this.rankings = []
      this.originalImage = ''
      this.leftImage = ''
      this.rightImage = ''

      this.$axios.patch(`/experiment-result/${this.experimentResult}/completed`)

      localStorage.removeItem(`${this.experiment.id}-index`)
      localStorage.removeItem(`${this.experiment.id}-experimentResult`)
      localStorage.removeItem(`${this.experiment.id}-stimuliQueue`)
      this.finished = true
    },

    abort () {
      localStorage.removeItem(`${this.experiment.id}-index`)
      localStorage.removeItem(`${this.experiment.id}-experimentResult`)
      localStorage.removeItem(`${this.experiment.id}-stimuliQueue`)
      this.abortDialog = true
      this.$router.push('/observer')
    }
  }
}
</script>

<style>
  /*html {
    overflow-y: auto;
  }*/
</style>

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

.draggable-title {
  position: absolute;
  // top: 32%;
  // left: 36%;
  top: 13%;
  left: 23%;
  z-index: 20;
  color: white;
  padding-top: 2px;
  padding-left: 5px;
  padding-bottom: 2px;
  padding-right: 5px;
  background: rgba(0,0,0,0.4);
}

.rating {
  overflow-x: auto;
  margin-right: 130px;
  margin-left: 130px;
}

.moveable-image {
  // width: 100px;
  width: 50px;
  position: relative;
  margin: 3px;
  cursor: move;
}

.list-group {
  display: flex;
}

.flip-list-move {
  transition: transform 0.5s;
}

.no-move {
  transition: transform 0s;
}

.ghost {
  opacity: 0.5;
}

.letter {
  cursor: pointer;
  background: rgba(0,0,0,0.2);
  color: #fff;
  padding: 5px;
}

.active {
  background: rgba(0,0,0,0.4);
}

.hide {
  opacity: 0;
}

.rating::-webkit-scrollbar {
  height: 0.5em;
}

.rating::-webkit-scrollbar-track {
  -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
}

.rating::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid slategrey;
}

// .sortable-drag {
//   opacity: 0;
// }
</style>
