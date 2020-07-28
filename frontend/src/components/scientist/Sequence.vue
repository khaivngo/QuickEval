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
          <v-layout justify-space-between>
            <v-flex xs12>
              <v-select
                v-if="event.type === 'imageSet'"
                :items="imageSets"
                v-model="event.value"
                item-text="title"
                item-value="id"
                label="Select image set"
                outlined
                dense
              >
                <template v-slot:append-outer>
                  <v-icon @click="remove(i)">mdi-delete</v-icon>
                </template>
              </v-select>

              <!-- <v-text-field
                v-if="event.type === 'instruction'"
                label="Write instruction"
                v-model="event.value"
                class="m-0"
              >
                <template v-slot:append-outer>
                  <v-icon @click="remove(i)">delete</v-icon>
                </template>
              </v-text-field> -->

              <v-row v-if="event.type === 'instruction'" align="center">
                <v-col>
                  <Tiptap v-model="event.value"/>
                </v-col>
                <v-col cols="auto">
                  <v-icon @click="remove(i)">
                    mdi-delete <!-- clear -->
                  </v-icon>
                </v-col>
              </v-row>

              <v-select
                v-if="event.type === 'instructionFromHistory'"
                label="Select instruction"
                :items="instructions"
                v-model="event.value"
                item-text="description"
                item-value="id"
                outlined
                dense
              >
                <template v-slot:append-outer>
                  <v-icon @click="remove(i)">mdi-delete</v-icon>
                </template>
              </v-select>
            </v-flex>
          </v-layout>
        </v-timeline-item>
      </v-slide-x-transition>
    </v-timeline>

    <v-row class="mb-5 pa-0 ma-0">
      <v-col cols="auto" class="pl-0">
        <v-menu bottom offset-y>
          <template v-slot:activator="{ on }">
            <v-btn
              class="mr-4 ml-0"
              color="info"
              v-on="on"
            >
              <v-icon class="mr-2" :size="20">mdi-plus-circle-outline</v-icon>
              instruction
            </v-btn>
          </template>

          <v-list>
            <v-list-item
              @click="add('instruction')"
            >
              <v-list-item-title>
                <v-icon left>mdi-plus</v-icon>
                Create new
              </v-list-item-title>
            </v-list-item>

            <v-list-item
              @click="add('instructionFromHistory')"
            >
              <v-list-item-title>
                <v-icon left>mdi-history</v-icon>
                Add from history
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>

      <v-col>
        <!-- <v-btn outlined @click="add('uploadImageSet')">
          upload image set <v-icon class="ml-2" small>create</v-icon>
        </v-btn> -->

        <!-- <v-btn depressed @click="add('imageSet')">
          image set <v-icon class="ml-2" :size="20">add</v-icon>
        </v-btn> -->

        <v-dialog v-model="openNewImageSet" persistent max-width="800">
          <!-- <template v-slot:activator="{ on }">
            <v-btn text dark color="#D9D9D9" v-on="{ on }">
              Create new
            </v-btn>
          </template> -->
          <v-card class="pa-4">
            <v-card-title class="headline">Create Image Set</v-card-title>
            <v-card-text>
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
                  <UppyOriginal :imagesetid="newImageSet.imageSetId" :width="300" :height="200"/>
                </v-layout>

                <v-layout mb-3>
                  <h2 class="title">Reproduction images</h2>
                </v-layout>

                <div>
                  <Uppy :imagesetid="newImageSet.imageSetId" style="width: 100%;">
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
            <v-card-actions class="mt-5">
              <v-spacer></v-spacer>
              <v-btn text @click="closeNewImageSet()">
                Done
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-menu bottom offset-y>
          <template v-slot:activator="{ on }">
            <v-btn
              color="info"
              v-on="on"
            >
              <v-icon class="mr-2" :size="20">mdi-plus-circle-outline</v-icon>
              image set
            </v-btn>
          </template>

          <v-list>
            <v-list-item
              @click.stop="openNewImageSet = true"
            >
              <v-list-item-title>
                <v-icon left small>mdi-pencil</v-icon>
                Create new
              </v-list-item-title>
            </v-list-item>

            <v-list-item
              @click="add('imageSet')"
            >
              <v-list-item-title>
                <v-icon left small>mdi-plus-circle</v-icon>
                Add existing
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
// import CreateImageSet from '@/views/scientist/image-set/CreateFile'
import UppyOriginal from '@/components/scientist/UppyOriginal'
import Uppy from '@/components/scientist/Uppy'
import Tiptap from '@/components/Tiptap'

export default {
  components: {
    // CreateImageSet,
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
      // immediate: true,
      handler (values) {
        values.forEach((item) => {
          let type  = (item.picture_queue_id != null) ? 'imageSet' : 'instruction'
          let value = (item.picture_queue_id != null) ? item.picture_set_id : item.description

          this.events.push({
            id: this.nonce++,
            value: value,
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

    openNewImageSet: false,
    newImageSet: {
      name: null,
      imageSetId: null,
      description: ''
    },
    creating: false
  }),

  async created () {
    // populate the instructions and image sets dropdowns
    const instructions = await this.getInstructions()
    this.instructions = instructions.data

    const imageSets = await this.getImageSets()
    this.imageSets = imageSets.data
  },

  methods: {
    // Since axios returns a promise the async/await can be omitted for the getData function
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
        type: type
      })

      this.input = null

      this.$emit('added', this.events)
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
