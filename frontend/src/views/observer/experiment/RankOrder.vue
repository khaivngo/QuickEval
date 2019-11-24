<template>
  <v-container fluid class="qe-wrapper" :style="'background-color: #' + experiment.background_colour">
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
      <!-- <h4 class="subheading font-weight-regular" v-if="experiment.show_original === 1">
        Original
      </h4> -->
    </v-layout>

    <v-layout ml-3 mr-3 pa-0 style="height: 72vh;" justify-center>
      <v-flex class="picture-container" mt-2 mb-1 ml-2 mr-2>
        <div class="panzoom">
          <img
            id="picture-left"
            class="picture"
            :class="isLoadLeft === false ? 'hide' : ''"
            @load="loadedLeft"
            :src="leftImage"
          />
        </div>
      </v-flex>

      <v-flex class="picture-container" mt-2 mb-1 ml-2 mr-2 v-if="experiment.show_original === 1">
        <div class="panzoom">
          <img
            id="picture-original"
            class="picture"
            :src="originalImage"
          />
        </div>
      </v-flex>

      <v-flex class="picture-container" mt-2 mb-1 ml-2 mr-2>
        <div class="panzoom">
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

    <v-layout mb-4>
      <v-layout justify-center class="ml-2 mr-2">
        <!-- Viewing image <strong>A</strong> -->
        <!-- <draggable group="stimuli">
          <img style="width: 100px; display: block;"/>
        </draggable> -->
        <div
          v-for="(element, i) in rankings"
          :key="element.id"
          @click="changeLeftPannerImage(element)"
          class="letter subheading pt-1 pb-1 pl-2 pr-2 ma-1"
          :class="(element.id === activeLeft) ? 'active' : ''"
        >
          {{ alphabet[i].toUpperCase() }}
        </div>
      </v-layout>

      <v-layout justify-center class="text-xs-center ml-2 mr-2" v-if="experiment.show_original === 1">
        <h4 class="body-1 font-weight-regular">
          Original
        </h4>
      </v-layout>

      <v-layout justify-center class="ml-2 mr-2">
        <!-- Viewing image <strong>C</strong> -->
        <div
          v-for="(element, i) in rankings"
          :key="element.id"
          @click="changeRightPannerImage(element)"
          class="letter subheading pt-1 pb-1 pl-2 pr-2 ma-1"
          :class="(element.id === activeRight) ? 'active' : ''"
        >
          {{ alphabet[i].toUpperCase() }}
        </div>
      </v-layout>
    </v-layout>

    <div class="rating">
      <v-layout justify-center align-center class="mt-2 pa-0">
        <!-- best -->
        <div class="text-xs-center subheading" v-for="(num, i) in rankings.length" :key="i"
          style="width: 100px; margin-left: 3px; margin-right: 3px; margin-top: 3px;"
        >
          <!-- #{{ (rankings.length + 1) - num }} -->
          {{ num }}
        </div>
        <!-- worst -->
      </v-layout>
      <v-layout justify-center class="mt-1 pa-0">
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
              <div class="draggable-title headline">
                {{ element.letter }}
              </div>
              <img style="width: 100px; display: block;" :src="`${$UPLOADS_FOLDER}${element.path}`"/>
            </div>
          </transition-group>
        </draggable>
      </v-layout>
      <!-- <v-layout justify-center align-center class="mb-2 pa-0"> -->
        <!-- best -->
        <!-- <div class="text-xs-center subheading" v-for="(num, i) in rankings.length" :key="i"
          style="width: 100px; margin-left: 3px; margin-right: 3px; margin-top: 3px;"
        > -->
          <!-- #{{ (rankings.length + 1) - num }} -->
          <!-- #{{ num }} -->
        <!-- </div> -->
        <!-- worst -->
      <!-- </v-layout> -->
    </div>

    <v-btn fixed bottom right color="#D9D9D9"
      @click="next()"
      :loading="disableNextBtn"
    >
      <span class="ml-1">next</span>
      <v-icon>keyboard_arrow_right</v-icon>
    </v-btn>

    <FinishedDialog :show="finished"/>
  </v-container>
</template>

<script>
import FinishedDialog from '@/components/observer/FinishedExperimentDialog'
import draggable from 'vuedraggable'

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
        stimuli_seperation_distance: 20,
        background_colour: '808080'
      },

      alphabet: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],

      stimuli: [],

      index: 0,
      experimentResult: null,

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
      rightImage: ''
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
        alert(err)
      })
    })
  },

  methods: {
    /**
     * Tag touched images. Used to decide if every image has been looked at.
     */
    dragEnd (e) {
      this.beingDragged = false
      // this.rankings[e.newIndex].moved = true
      // e.moved.element.moved = true
    },

    changeLeftPannerImage (element) {
      this.isLoadLeft = false

      this.$nextTick(() => {
        this.leftImage = this.$UPLOADS_FOLDER + element.path
      })

      this.activeLeft = element.id
      // tag the stimuli as watched
      this.rankings.find(e => e.id === element.id).watched = true
    },

    loadedLeft () {
      window.setTimeout(() => {
        this.isLoadLeft = true
      }, 200)
    },

    changeRightPannerImage (element) {
      this.isLoadRight = false

      this.$nextTick(() => {
        this.rightImage = this.$UPLOADS_FOLDER + element.path
      })

      this.activeRight = element.id
      // tag the stimuli as watched
      this.rankings.find(e => e.id === element.id).watched = true
    },

    loadedRight () {
      window.setTimeout(() => {
        this.isLoadRight = true
      }, 300)
    },

    /**
     * Load the next image set stimuli, or instructions.
     */
    next () {
      // Have we reached the end?
      if (this.stimuli[this.index] === undefined) {
        this.onFinish()
        return
      }

      // this.disableNextBtn = true

      if (this.stimuli[this.index].hasOwnProperty('picture_queue')) {
        //
        this.rankings = this.stimuli[this.index].picture_queue
        this.activeLeft = this.stimuli[this.index].picture_queue[0].id
        this.activeRight = this.stimuli[this.index].picture_queue[1].id
        this.rankings[0].watched = true
        this.rankings[1].watched = true

        // give each image a letter ID
        this.rankings.forEach((item, i) => {
          this.$set(item, 'letter', this.alphabet[i].toUpperCase())
        })

        // set original
        if (this.stimuli[this.index].picture_queue[0].hasOwnProperty('original')) {
          this.originalImage = this.$UPLOADS_FOLDER + this.stimuli[this.index].picture_queue[0].original.path
        }

        // set left reproduction image
        this.getImage(this.stimuli[this.index].picture_queue[0].picture_id).then(image => {
          this.leftImage = this.$UPLOADS_FOLDER + image.data.path
        })

        // set right reproduction image
        this.getImage(this.stimuli[this.index].picture_queue[1].picture_id).then(image => {
          this.rightImage = this.$UPLOADS_FOLDER + image.data.path
        })

        let watched = this.rankings.filter(element => element.hasOwnProperty('watched'))
        if (this.rankings.length !== 0 && (watched.length === this.rankings.length)) {
          this.disableNextBtn = true

          this.store().then(response => {
            if (response.data !== 'result_stored') {
              alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
            }

            this.disableNextBtn = false
            this.index += 1
            localStorage.setItem('index', this.index)

            // Have we reached the end?
            console.log(this.stimuli[this.index])
            if (this.stimuli[this.index] === undefined) {
              this.onFinish()
            }
          }).catch(() => {
            this.disableNextBtn = false
            alert('Could not save your answer. Please try again. If the problem persist please contact the researcher.')
          })
        }
      } else {
        this.instructionText = this.stimuli[this.index].instructions[0].description
        this.instructionDialog = true

        this.index += 1
        localStorage.setItem('index', this.index)

        this.next()
      }
    },

    async getExperiment (experimentId) {
      return this.$axios.get(`/experiment/${experimentId}`)
    },

    async getImage (id) {
      return this.$axios.get(`/picture/${id}`)
    },

    async store () {
      let data = {
        experiment_result_id: this.experimentResult,
        rankings: this.rankings
      }

      return this.$axios.post('/rank-order-result', data)
    },

    onFinish () {
      localStorage.removeItem('index')
      localStorage.removeItem('experimentResult')
      this.finished = true
    },

    abort () {
      localStorage.removeItem('index')
      localStorage.removeItem('experimentResult')
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

.draggable-title {
  position: absolute;
  top: 32%;
  left: 36%;
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
  width: 100px;
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
