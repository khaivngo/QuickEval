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
            <v-col class="pa-0 ma-0 pr-6">
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
                    label="Select stimuli group"
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
                        Randomize order of stimuli within stimuli set.
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
                        Display the original stimulus of the stimuli group alongside the reproductions.<br>
                        As a reference for the observer.
                      </div>
                    </v-tooltip>
                  </div>
                </v-col>

                <v-col cols="auto" class="pl-6 pr-0 pt-0 pb-0 mr-0">
                  <div class="d-flex flex-column align-center">
                    <h6 class="caption" style="margin-bottom: -6px;">More</h6>
                    <v-dialog v-model="displayExtraSettings[i+k]" max-width="500">
                      <template v-slot:activator="{ on }">
                        <v-btn v-on="on" icon class="ma-0pa-0" style="margin-bottom: -3px;">
                          <v-icon>mdi-dots-horizontal-circle-outline</v-icon>
                        </v-btn>
                      </template>
                      <v-card>
                        <v-card-title class="headline">
                          Extra Settings
                        </v-card-title>

                        <v-card-text>
                          <v-row class="mt-4" align="center">
                            <v-col cols="10">
                              <v-text-field
                                v-model.number="event.hideImageTimer"
                                label="Hide image after"
                                outlined
                                dense
                                suffix="milliseconds"
                                @keydown.enter="displayExtraSettings[i+k] = false"
                              ></v-text-field>
                            </v-col>
                            <v-col
                              cols="2"
                              class="pa-0 mb-1"
                            >
                              <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                  <v-btn icon v-on="on">
                                    <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                                  </v-btn>
                                </template>
                                <div class="pl-2 pr-2 pt-3 pb-3 body-1">
                                  Show the stimuli group's stimuli for the specified time before hiding it.
                                  If the observer does not rate the image before the time runs out,
                                  they will have to rate it based on their memory.
                                  Keep field empty to never hide.
                                </div>
                              </v-tooltip>
                            </v-col>
                          </v-row>
                        </v-card-text>

                        <v-card-actions>
                          <v-spacer></v-spacer>
                          <v-btn
                            color="primary"
                            text
                            @click="closeExtraSettings(i+k)"
                          >
                            Done
                          </v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                  </div>
                </v-col>
              </v-row>
            </v-col>
            <v-col v-if="group.length > 1" cols="auto" class="pa-0 ma-0 mt-6 mb-0" style="border-left: 1px solid #999;">
              <v-container fluid fill-height class="pa-0 ma-0 pl-5 pr-5">
                <v-col cols="auto">
                  <div class="d-flex flex-column align-center">
                    <h6 class="caption text-center">Randomize<br>group order</h6>
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
                        Randomize order of stimuli groups.
                      </div>
                    </v-tooltip>
                  </div>
                </v-col>
              </v-container>
            </v-col>
            <v-col v-if="group.length > 1" cols="auto" class="pa-0 ma-0 mt-6 mb-0" style="border-left: 1px solid #999;">
              <v-container fluid fill-height class="pa-0 ma-0 pl-5">
                <v-col cols="auto">
                  <div class="d-flex flex-column align-center">
                    <h6 class="caption text-center">Randomize<br>across groups</h6>
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-checkbox
                          v-on="on"
                          v-model="group[0].randomizeAcross"
                          class="ma-0 pa-0 pl-1"
                          color="success"
                          hide-details
                        ></v-checkbox>
                      </template>
                      <div class="pl-0 pr-0 pt-1 pb-1 body-1">
                        Randomize order of stimuli across stimuli groups.<br>This randomizes everything,
                        except for the relationship between stimuli and instructions.
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
          stimuli group
        </v-btn>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on" class="mr-2 ml-1">
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
          Create Stimuli Group
        </v-card-title>
        <v-card-text class="pt-8 pb-8" style="color: #000;">
          <v-layout align-center>
            <v-text-field
              v-model="newImageSet.name"
              label="Title"
              outlined
              dense
              autofocus
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

          <!-- <div class="mt-5" :class="(!newImageSet.imageSetId) ? 'not-interactable' : ''">
            <v-layout mb-3>
              <h2 class="title">
                Original/reference image
                <span class="subheading">(optional)</span>
              </h2>
            </v-layout>

            <v-layout>
              <p>
                Upload the original, uncompressed, image of the image set.
                During experiment creation you'll be able to select whether or not
                this image should be shown to the observer.
              </p>
            </v-layout>

            <v-layout mb-5>
              <UppyOriginal
                :imagesetid="newImageSet.imageSetId"
                @uploaded="addOriginal"
                :width="300" :height="200"
              >
                <div v-show="original.length === 0" id="UppyModalOpenerBtnOriginal">
                  <v-icon color="primary" large>mdi-plus</v-icon>
                </div>
              </UppyOriginal>
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
          </div> -->

          <div class="pl-4 pr-4" :class="(!newImageSet.imageSetId) ? 'not-interactable' : ''">
            <div class="mt-4 pt-6">
              <v-row align="center">
                <v-col cols="auto" class="pa-0 ma-0">
                  <h2 class="text-h6">Stimuli</h2>
                </v-col>

                <v-col
                  class="pa-0 ma-0"
                  :class="reproductions.length === 0 ? 'pt-4' : ''"
                  :cols="reproductions.length === 0 ? 12 : 'auto'"
                >
                  <Uppy :imagesetid="newImageSet.imageSetId" @uploaded="addImage">
                    <v-btn v-show="reproductions.length > 0" color="primary" id="UppyModalOpenerBtn" outlined icon text class="ml-6 mt-1">
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                    <div v-show="reproductions.length === 0" id="UppyModalOpenerBtn" class="default">
                      <v-icon color="primary" large>mdi-plus</v-icon>
                    </div>
                  </Uppy>
                </v-col>
              </v-row>

              <v-row wrap>
                <v-col
                  v-for="(image, i) in reproductions" :key="i"
                  xs="6" sm="6" md="3" lg="3" xl="3"
                  class="pa-1"
                >
                  <div style="display: flex; justify-content: flex-end;">
                    <ActionMenu :image="image" :index="i" @deleted="deleteImage"/>
                  </div>

                  <v-img
                    v-if="isImage(image.extension)"
                    :src="$UPLOADS_FOLDER + image.path"
                    aspect-ratio="1"
                    class="grey lighten-2"
                  >
                    <template v-slot:placeholder>
                      <v-layout
                        fill-height
                        align-center
                        justify-center
                        ma-0
                      >
                        <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                      </v-layout>
                    </template>
                  </v-img>
                  <video
                    v-if="isVideo(image.extension)"
                    loop controls
                    style="width: 100%;"
                    class="video-player"
                  >
                    <!-- <source :src="image.path" :type="'video/'+leftExtension"> -->
                    <source :src="$UPLOADS_FOLDER + image.path" :type="`video/${image.extension}`">
                    Your browser does not support the video tag.
                  </video>

                  <h5 class="subtitle-2 text-center qe-image-name mt-2 mb-2">
                    {{ image.name }}
                  </h5>
                </v-col>
              </v-row>
            </div>

            <div class="mt-12 pt-12">
              <v-row v-if="original.length > 0">
                <v-col cols="12" class="pl-0 ml-0">
                  <h3 class="text-h6">
                    Reference/original
                  </h3>
                </v-col>
                <v-col
                  xs="6" sm="6" md="3" lg="3" xl="3"
                  class="pa-1"
                >
                  <div style="display: flex; justify-content: flex-end;">
                    <v-menu offset-y left>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          icon
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon>mdi-dots-horizontal</v-icon>
                        </v-btn>
                      </template>

                      <v-list>
                        <v-list-item @click="deleteOriginal(original[0].id)">
                          <v-list-item-icon class="mr-4">
                            <v-icon>mdi-delete</v-icon>
                          </v-list-item-icon>
                          <v-list-item-content>
                            <v-list-item-title class="pr-5">Delete</v-list-item-title>
                          </v-list-item-content>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </div>

                  <v-img
                    v-if="isImage(original[0].extension)"
                    :src="$UPLOADS_FOLDER + original[0].path"
                    aspect-ratio="1"
                    class="grey lighten-2"
                  >
                    <template v-slot:placeholder>
                      <v-layout
                        fill-height
                        align-center
                        justify-center
                        ma-0
                      >
                        <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                      </v-layout>
                    </template>
                  </v-img>
                  <video
                    v-if="isVideo(original[0].extension)"
                    loop controls
                    style="width: 100%;"
                    class="video-player"
                  >
                    <!-- <source :src="image.path" :type="'video/'+leftExtension"> -->
                    <source :src="$UPLOADS_FOLDER + original[0].path" :type="`video/${original[0].extension}`">
                    Your browser does not support the video tag.
                  </video>

                  <h5 class="subtitle-2 text-center qe-image-name mt-2 mb-2">
                    {{ original[0].name }}
                  </h5>
                </v-col>
              </v-row>

              <div v-if="original.length === 0" class="ma-0 pa-0">
                <v-row align="center">
                  <h2 class="text-h6 mb-2 pl-0 ml-0">
                    Reference/original
                    <span class="body-1">(optional)</span>
                  </h2>
                </v-row>

                <v-row class="mb-6" align="center">
                  <p class="ma-0">
                    Upload the original, uncompressed, stimulus of the stimuli group.
                  </p>
                  <v-tooltip top>
                    <template v-slot:activator="{ on }">
                      <v-btn icon v-on="on">
                        <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                      </v-btn>
                    </template>
                    <div class="pl-2 pr-2 pt-3 pb-3 pb-5 body-1">
                      During experiment creation you'll be able to select whether or not
                      this image should be shown to the observer.
                    </div>
                  </v-tooltip>
                </v-row>
              </div>

              <v-row class="mb-5">
                <UppyOriginal
                  :imagesetid="newImageSet.imageSetId"
                  @uploaded="addOriginal"
                  :width="300"
                  :height="200"
                >
                  <div v-show="original.length === 0" id="UppyModalOpenerBtnOriginal">
                    <v-icon color="primary" large>mdi-plus</v-icon>
                  </div>
                </UppyOriginal>
              </v-row>
            </div>
          </div>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            text
            @click="openNewImageSet = false"
          >
            Cancel
          </v-btn>
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
import ActionMenu from '@/components/scientist/ActionMenu'
import EventBus from '@/eventBus'
import mixin from '@/mixins/FileFormats.js'

import { storage } from '@/stores/store.js'

export default {
  components: {
    UppyOriginal,
    Uppy,
    Tiptap,
    ActionMenu
  },

  mixins: [mixin],

  props: {
    sequences: {
      type: Array,
      default: function () {
        return []
      }
    },
    sets: {
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
            randomize: Number(item.randomize),
            randomizeGroup: Number(item.randomize_group),
            randomizeAcross: Number(item.randomize_across),
            original: Number(item.original),
            flipped: Number(item.flipped),
            hideImageTimer: item.hide_image_timer,
            type: type
          })

          // this.input = null
          this.$emit('added', this.eventsGrouped)
        })
      }
    },
    sets: {
      immediate: true,
      handler (values) {
        this.imageSets.push(...values)
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
      name: '',
      imageSetId: null,
      description: ''
    },
    creating: false,

    original: [],
    reproductions: [],

    displayExtraSettings: {}
  }),

  computed: {
    /**
     * Group adjucent event types. So that we can now display a "randomize" order option
     * for a image set group inbetween instructions.
     *
     * @input  [instruction, imageSet, imageSet]
     * @output [[instruction], [imageSet, imageSet]]
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
    this.imageSets.push(...imageSets.data)
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
        randomizeAcross: false,
        original: false,
        flipped: false,
        hideImageTimer: null,
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

    closeExtraSettings (id) {
      this.displayExtraSettings[id] = false
    },

    addOriginal (files) {
      console.log(files)
      this.original.unshift(files[0])
    },

    addImage (files) {
      console.log(files)
      this.reproductions.unshift(files[0])
    },

    deleteImage (index) {
      this.reproductions.splice(index, 1)
      EventBus.$emit('success', 'Image has been deleted successfully')
    },

    deleteOriginal (id) {
      // this.deleting = true
      if (confirm('Delete image?')) {
        this.$axios.delete(`/picture/${id}`).then((response) => {
          this.original.splice(0, 1)
          EventBus.$emit('success', 'Image has been deleted successfully')
          this.deleting = false
        }).catch(() => {
          this.deleting = false
        })
      }
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
          randomizeAcross: false,
          original: false,
          flipped: false,
          hideImageTimer: null,
          type: 'imageSet'
        })

        this.$emit('added', this.eventsGrouped)

        this.creating = false
      }).catch(() => {
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
