<template>
  <v-container fluid class="pa-0">
    <v-timeline dense clipped v-if="events.length > 0">
      <!-- <v-timeline-item fill-dot class="white--text mb-5" color="grey" large>
        <template v-slot:icon>
          <span>Step</span>
        </template>
      </v-timeline-item> -->

      <v-slide-x-transition group>
        <v-timeline-item
          v-for="(event, i) in events"
          :key="event.id"
          class="mb-3"
          color="grey lighten-1"
          medium
          fill-dot
        >
          <template v-slot:icon>
            <span class="white--text">{{ i + 1 }}</span>
          </template>

          <!-- <div v-if="events[i-1] && events[i-1].type === 'imageSet' && event.type === 'imageSet'">www</div> -->
          <v-row v-if="event.type === 'imageSet'" align="center" class="ma-0 pa-0">
            <v-col cols="auto" class="pa-0 ma-0">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on" class="mr-2">
                    <v-icon @click="remove(i)">
                      mdi-delete
                    </v-icon>
                  </v-btn>
                </template>
                <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                  Remove
                </div>
              </v-tooltip>
            </v-col>

            <v-col>
              <v-select
                v-if="event.type === 'imageSet'"
                :items="imageSets"
                v-model="event.value"
                item-text="title"
                item-value="id"
                label="Select image set"
                outlined
                dense
                hide-details
                class="ma-0"
              ></v-select>
            </v-col>

            <!-- <v-col cols="auto" class="pa-0 pl-4 pr-6 ma-0" justify="center">
              <h6 class="caption">Randomize</h6>
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-checkbox
                    v-on="on"
                    v-model="event.randomize"
                    class="ma-0"
                    color="success"
                  ></v-checkbox>
                </template>
                <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                  Randomize order of stimuli.
                </div>
              </v-tooltip>
            </v-col>

            <v-col cols="auto" class="pa-0 pr-6 ma-0" justify="center">
              <h6 class="caption">Flipped</h6>
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-checkbox
                    v-on="on"
                    v-model="event.flipped"
                    class="ma-0"
                    color="success"
                  ></v-checkbox>
                </template>
                <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                  Each pair of images will have their position flipped in the queue.<br>
                  Leading to double the comparisons for the observer.
                </div>
              </v-tooltip>
            </v-col>

            <v-col cols="auto" class="pa-0 ma-0" justify="center">
              <h6 class="caption">Original</h6>
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-checkbox
                    v-on="on"
                    v-model="event.original"
                    class="ma-0"
                    color="success"
                  ></v-checkbox>
                </template>
                <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                  Display the original image of the image set alongside the reproductions.<br>
                  As a reference for the observer.
                </div>
              </v-tooltip>
            </v-col> -->
          </v-row>

          <v-row v-if="event.type === 'instruction'" align="center">
            <v-col cols="auto" class="pa-0 ma-0">
              <div>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on" class="mr-2">
                      <v-icon @click="remove(i)">
                        mdi-delete
                      </v-icon>
                    </v-btn>
                  </template>
                  <div class="pl-1 pr-1 pt-2 pb-2 body-1">
                    Remove
                  </div>
                </v-tooltip>
              </div>
            </v-col>

            <v-col class="pr-0">
              <Tiptap v-model="event.value"/>
            </v-col>
          </v-row>

          <!-- <v-layout justify-space-between align-center class="pa-0 ma-0">
            <v-flex xs12 align-center pa-0 ma-0>
              <v-select
                v-if="event.type === 'imageSet'"
                :items="imageSets"
                v-model="event.value"
                item-text="title"
                item-value="id"
                label="Select image set"
                outlined
                dense
                hide-details
                class="ma-0"
              >
                <template v-slot:append-outer>
                  <div>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on" class="mr-2">
                          <v-icon @click="remove(i)">
                            mdi-delete
                          </v-icon>
                        </v-btn>
                      </template>
                      <div class="pl-1 pr-1 pt-2 pb-2 body-1">
                        Remove
                      </div>
                    </v-tooltip>
                  </div>
                </template>
              </v-select>

              <v-row align="center" class="mt-4 pt-0">
                <v-col cols="auto" class="pt-0 pb-0 pr-0">
                  <v-checkbox
                    color="success"
                    :label="`Display original image`"
                  ></v-checkbox>
                </v-col>
                <v-col cols="auto" class="pa-0 mb-1">
                  <v-tooltip top>
                    <template v-slot:activator="{ on }">
                      <v-btn icon v-on="on">
                        <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                      </v-btn>
                    </template>
                    <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                      Display the original image of the image set alongside the reproductions.<br>
                      As a reference for the observer.
                    </div>
                  </v-tooltip>
                </v-col>
              </v-row>

              <v-row v-if="event.type === 'instruction'" align="center">
                <v-col class="pr-0">
                  <Tiptap v-model="event.value"/>
                </v-col>
                <v-col cols="auto">
                  <div>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on" class="mr-2">
                          <v-icon @click="remove(i)">
                            mdi-delete
                          </v-icon>
                        </v-btn>
                      </template>
                      <div class="pl-1 pr-1 pt-2 pb-2 body-1">
                        Remove
                      </div>
                    </v-tooltip>
                  </div>
                </v-col>
              </v-row>
            </v-flex>
          </v-layout> -->
        </v-timeline-item>
      </v-slide-x-transition>
    </v-timeline>

    <v-row class="mb-5 pa-0 ma-0">
      <v-col cols="auto" class="pl-0">
        <v-btn
          class="mr-1 ml-0"
          color="info"
          @click="add('instruction')"
        >
          <v-icon class="mr-2" :size="20">mdi-format-list-bulleted</v-icon>
          instruction
        </v-btn>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-icon @click="openInstructionsHistory = true">
                mdi-history
              </v-icon>
            </v-btn>
          </template>
          <div class="pl-1 pr-1 pt-2 pb-2 body-1">
            Add from history
          </div>
        </v-tooltip>
      </v-col>

      <v-col>
        <v-btn
          color="info"
          @click="add('imageSet')"
        >
          <v-icon class="mr-2" :size="20">mdi-tooltip-image-outline</v-icon>
          image set
        </v-btn>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on" class="mr-2">
              <v-icon @click="openNewImageSet = true">
                mdi-plus
              </v-icon>
            </v-btn>
          </template>
          <div class="pl-1 pr-1 pt-2 pb-2 body-1">
            Create new set
          </div>
        </v-tooltip>
      </v-col>
    </v-row>

    <v-dialog v-model="openNewImageSet" persistent scrollable max-width="800">
      <v-card>
        <v-card-title class="headline" style="border-bottom: 1px solid #ddd;">
          Create Image Set
        </v-card-title>
        <v-card-text class="pt-8 pb-8">
          <v-layout align-center>
            <v-text-field
              v-model="newImageSet.name"
              label="Title"
              outlined
              dense
            ></v-text-field>

            <v-btn
              :disabled="newImageSet.name === '' || creating"
              :loading="creating"
              color="success"
              class="ma-0 ml-3"
              @click="createNew"
            >
              Create
            </v-btn>
          </v-layout>

          <div class="mt-5" :class="(!newImageSet.imageSetId) ? 'not-interactable' : ''">
            <v-layout mb-3>
              <h2 class="title">
                Original/reference image
                <span class="subheading">(optional)</span>
              </h2>
            </v-layout>

            <v-layout>
              <p>
                Upload the original, uncompressed, image of the image set.
                During experiment creation you'll be able to select whether or not this image should be shown to the observer.
              </p>
            </v-layout>

            <v-layout mb-5>
              <UppyOriginal :imagesetid="newImageSet.imageSetId" @uploaded="addOrginal" :width="300" :height="200"/>
            </v-layout>

            <v-layout mb-3>
              <h2 class="title">Reproduction images</h2>
            </v-layout>

            <div>
              <Uppy :imagesetid="newImageSet.imageSetId" @uploaded="addImage" style="width: 100%;">
                <div id="UppyModalOpenerBtn" class="default">
                  <v-icon color="primary" large>mdi-plus</v-icon>
                </div>
              </Uppy>
            </div>

            <!-- <v-layout mt-5 justify-end>
              <v-btn class="success" @click="openNewImageSet = false">
                Done
              </v-btn>
            </v-layout> -->
          </div>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-btn
            color="blue darken-1"
            text
            @click="openNewImageSet = false"
          >
            Cancel
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn color="#78AA1C" dark @click="closeNewImageSet()">
            Done
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="openInstructionsHistory" persistent scrollable max-width="800">
      <v-card>
        <v-card-title class="headline" style="border-bottom: 1px solid #ddd;">
          Select instructions
        </v-card-title>
        <v-card-text class="pt-8 pb-8">
          <div
            v-for="(instruction, i) in instructions" :key="i"
            v-html="instruction.description"
            style="height: 60px; overflow-y: hidden; margin-bottom: 10px; padding: 10px; background-color: #eee; cursor: pointer; border-radius: 4px;"
            @click="instructionFromHistory(instruction)"
          ></div>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            text
            @click="openInstructionsHistory = false"
          >
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import UppyOriginal from '@/components/scientist/UppyOriginal'
import Uppy from '@/components/scientist/Uppy'
import Tiptap from '@/components/Tiptap'

export default {
  components: {
    UppyOriginal,
    Uppy,
    Tiptap
  },

  props: {
    sequences: {
      type: Array,
      default: function () {
        return []
      }
    }
  },

  watch: {
    sequences: {
      immediate: true,
      handler (values) {
        values.forEach((item) => {
          let type  = (item.picture_queue_id != null) ? 'imageSet' : 'instruction'
          let value = (item.picture_queue_id != null) ? Number(item.picture_set_id) : item.description

          this.events.push({
            id: this.nonce++,
            value: value,
            randomize: true,
            original: false,
            flipped: false,
            type: type
          })

          // this.input = null
          this.$emit('added', this.events)
        })
      }
    }
  },

  data: () => ({
    // existing instructions
    instructions: [],
    // existing image sets
    imageSets: [],
    // selected instructions and image sets, new and existing
    events: [],
    input: null,
    nonce: 0,

    openInstructionsHistory: false,
    openNewImageSet: false,
    newImageSet: {
      name: null,
      imageSetId: null,
      description: ''
    },
    creating: false,

    orginal: [],
    reproductions: []
  }),

  async created () {
    // populate the instructions and image sets dropdowns
    const instructions = await this.getInstructions()
    this.instructions = instructions.data

    const imageSets = await this.getImageSets()
    this.imageSets = imageSets.data
  },

  methods: {
    // Since axios returns a promise the async/await can be omitted
    getInstructions () {
      try {
        return this.$axios.get('/instructions')
      } catch (error) {
        console.log(error)
        return null
      }
    },

    getImageSets () {
      try {
        return this.$axios.get('/image-sets')
      } catch (error) {
        console.log(error)
        return null
      }
    },

    add (type) {
      this.events.push({
        id: this.nonce++,
        value: this.input,
        randomize: true,
        original: false,
        flipped: false,
        type: type
      })

      this.input = null

      this.$emit('added', this.events)
    },

    instructionFromHistory (instruction) {
      this.events.push({
        id: this.nonce++,
        value: instruction.description,
        type: 'instruction'
      })

      // this.input = null
      this.$emit('added', this.events)
      this.openInstructionsHistory = false
    },

    remove (id) {
      this.events.splice(id, 1)
    },

    closeNewImageSet () {
      this.openNewImageSet = false
      // this.newImageSet.imageSetId = null
      // this.newImageSet.name = null

      // removeFile in uppy
    },

    addOrginal (files) {
      this.original.unshift(files[0])
    },

    addImage (files) {
      this.reproductions.unshift(files[0])
    },

    createNew () {
      this.creating = true

      const data = {
        title: this.newImageSet.name,
        description: this.newImageSet.description
      }

      this.$axios.post('/imageSet', data).then((response) => {
        this.newImageSet.imageSetId = response.data.id
        this.imageSets.push(response.data)

        this.events.push({
          id: this.nonce++,
          value: response.data.id,
          randomize: true,
          original: false,
          flipped: false,
          type: 'imageSet'
        })
        this.$emit('added', this.events)

        this.creating = false
      })
    }
  }
}
</script>

<style lang="css">
.v-text-field__details {
  display: none;
}

/* remove line through number steps */
/*.v-timeline:before {
  width: 0px;
}*/
</style>

<style scoped lang="css">
  .not-interactable {
    pointer-events: none;
    opacity: 0.3;
  }
</style>
