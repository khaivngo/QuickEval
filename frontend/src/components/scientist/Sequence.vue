<template>
  <v-container fluid class="pa-0">
    <v-slide-x-transition group>
      <div
        v-for="(group, i) in eventsGrouped"
        :key="i"
        class="mb-3"
        color="grey lighten-1"
      >
        <template v-if="group[0].type === 'imageSet'">
          <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0 pr-12">
              <v-row v-for="(event, k) in group" :key="k" align="center" class="ma-0 pa-0 mt-6">
                <!-- <v-col cols="auto" class="pa-0 ma-0 pr-4">
                  <div class="qe-step-circle d-flex justify-center align-center elevation-1">
                    {{ event.id + 1 }}
                  </div>
                </v-col> -->

                <v-col cols="auto" class="pa-0 ma-0">
                  <v-tooltip top>
                    <template v-slot:activator="{ on }">
                      <v-btn icon v-on="on" class="mr-2">
                        <v-icon @click="remove(event.id)">
                          mdi-delete
                        </v-icon>
                      </v-btn>
                    </template>
                    <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                      Remove
                    </div>
                  </v-tooltip>
                </v-col>

                <v-col class="pa-0 ma-0">
                  <v-select
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

                <v-col cols="auto" class="pa-0 ma-0 pl-4 pr-6">
                  <div class="d-flex flex-column align-center">
                    <h6 class="caption">Randomize</h6>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-checkbox
                          v-on="on"
                          v-model="event.randomize"
                          class="ma-0 pa-0 pb-1 pl-1"
                          color="success"
                          hide-details
                        ></v-checkbox>
                      </template>
                      <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                        Randomize order of stimuli.
                      </div>
                    </v-tooltip>
                  </div>
                </v-col>

                <v-col v-if="storage.experimentType === 1" cols="auto" class="pa-0 pr-6 ma-0">
                  <div class="d-flex flex-column align-center">
                    <h6 class="caption">Flipped</h6>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-checkbox
                          v-on="on"
                          v-model="event.flipped"
                          class="ma-0 pa-0 pb-1 pl-1"
                          color="success"
                          hide-details
                        ></v-checkbox>
                      </template>
                      <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                        Each pair of images will have their position flipped in the queue.<br>
                        Leading to double the comparisons for the observer.
                      </div>
                    </v-tooltip>
                  </div>
                </v-col>

                <v-col cols="auto" class="pa-0 ma-0" justify="center">
                  <div class="d-flex flex-column align-center">
                    <h6 class="caption">Original</h6>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-checkbox
                          v-on="on"
                          v-model="event.original"
                          class="ma-0 pa-0 pb-1 pl-1"
                          color="success"
                          hide-details
                        ></v-checkbox>
                      </template>
                      <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                        Display the original image of the image set alongside the reproductions.<br>
                        As a reference for the observer.
                      </div>
                    </v-tooltip>
                  </div>
                </v-col>
              </v-row>
            </v-col>
            <v-col v-if="group.length > 1" cols="auto" class="pa-0 ma-0 mt-6 mb-0" style="border-left: 1px solid #999;">
              <v-container fluid fill-height class="pa-0 ma-0 pl-6">
                <v-col cols="auto">
                  <div class="d-flex flex-column align-center">
                    <h6 class="caption">Randomize</h6>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-checkbox
                          v-on="on"
                          v-model="group[0].randomizeGroup"
                          class="ma-0 pa-0 pl-1"
                          color="success"
                          hide-details
                        ></v-checkbox>
                      </template>
                      <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                        Randomize order of image sets.
                      </div>
                    </v-tooltip>
                  </div>
                </v-col>
              </v-container>
            </v-col>
          </v-row>
        </template>

        <template v-if="group[0].type === 'instruction'">
          <v-row v-for="(event, k) in group" :key="k" class="pa-0 ma-0 mt-8" align="center">
            <!-- <v-col cols="auto" class="pa-0 ma-0 pr-4">
              <div class="qe-step-circle d-flex justify-center align-center elevation-1">
                {{ event.id + 1 }}
              </div>
            </v-col> -->

            <v-col cols="auto" class="pa-0 ma-0">
              <div>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on" class="mr-2">
                      <v-icon @click="remove(event.id)">
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

            <v-col class="pa-0 ma-0">
              <Tiptap v-model="event.value"/>
            </v-col>
          </v-row>
        </template>
      </div>
    </v-slide-x-transition>

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
          <div class="pl-1 pr-1 pt-2 ml-2 pb-2 body-1">
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

import { storage } from '@/stores/store.js'

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
            randomize: item.randomize,
            randomizeGroup: item.randomize_group,
            original: item.original,
            flipped: item.flipped,
            type: type
          })

          // this.input = null
          this.$emit('added', this.eventsGrouped)
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

    storage: storage,

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

  computed: {
    /**
     * Group adjucent event types:
     * [instruction, imageSet, imageSet] --> [[instruction], [imageSet, imageSet]]
     * We can now display a "randomize" order option for a image set group
     * inbetween instructions.
     */
    eventsGrouped () {
      return this.events.reduce(function (prev, curr) {
        if (prev.length && curr.type === prev[prev.length - 1][0].type) {
          prev[prev.length - 1].push(curr)
        } else {
          prev.push([curr])
        }
        return prev
      }, [])
    }
  },

  async created () {
    // populate the instructions...
    const instructions = await this.getInstructions()
    this.instructions = instructions.data

    // ...and the image sets dropdowns
    const imageSets = await this.getImageSets()
    this.imageSets = imageSets.data
  },

  methods: {
    // Since axios returns a promise the async/await can be omitted
    getInstructions () {
      try {
        return this.$axios.get('/instructions')
      } catch (error) {
        alert(error)
        return null
      }
    },

    getImageSets () {
      try {
        return this.$axios.get('/picture-set')
      } catch (error) {
        alert(error)
        return null
      }
    },

    add (type) {
      this.events.push({
        id: this.nonce++,
        value: this.input,
        randomize: true,
        randomizeGroup: false,
        original: false,
        flipped: false,
        type: type
      })

      this.input = null

      // this.$emit('added', this.events)
      this.$emit('added', this.eventsGrouped)
    },

    instructionFromHistory (instruction) {
      this.events.push({
        id: this.nonce++,
        value: instruction.description,
        type: 'instruction'
      })

      // this.input = null
      this.$emit('added', this.eventsGrouped)
      this.openInstructionsHistory = false
    },

    remove (id) {
      const index = this.events.findIndex(obj => obj.id === id)
      this.events.splice(index, 1)
      this.$emit('added', this.eventsGrouped)
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

      this.$axios.post('/picture-set', data).then((response) => {
        this.newImageSet.imageSetId = response.data.id
        this.imageSets.push(response.data)

        this.events.push({
          id: this.nonce++,
          value: response.data.id,
          randomize: true,
          randomizeGroup: false,
          original: false,
          flipped: false,
          type: 'imageSet'
        })

        this.$emit('added', this.eventsGrouped)

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
  .qe-step-circle {
    background: #BDBDBD;
    color: #fff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
  }
</style>
