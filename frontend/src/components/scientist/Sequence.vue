<template>
  <v-container>
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
              >
                <template v-slot:append-outer>
                  <v-icon @click="remove(i)">delete</v-icon>
                </template>
              </v-select>

              <v-text-field
                v-if="event.type === 'instruction'"
                label="Write instruction"
                v-model="event.value"
                class="m-0"
              >
                <template v-slot:append-outer>
                  <v-icon @click="remove(i)">delete</v-icon>
                </template>
              </v-text-field>

              <v-select
                v-if="event.type === 'instructionFromHistory'"
                label="Select instruction"
                :items="instructions"
                v-model="event.value"
                item-text="description"
                item-value="id"
              >
                <template v-slot:append-outer>
                  <v-icon @click="remove(i)">delete</v-icon>
                </template>
              </v-select>
            </v-flex>
          </v-layout>
        </v-timeline-item>
      </v-slide-x-transition>
    </v-timeline>

    <v-layout class="mb-5" justify-center wrap>
      <v-flex>
        <v-menu open-on-hover bottom offset-y>
          <template v-slot:activator="{ on }">
            <v-btn
              class="text-none"
              color="info"
              v-on="on"
            >
              Add instruction
              <v-icon class="ml-2" :size="20">add</v-icon>
            </v-btn>
          </template>

          <v-list>
            <v-list-tile @click="add('instruction')">
              <v-list-tile-title class="pr-3">
                <v-icon left small>create</v-icon>
                Create new
              </v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="add('instructionFromHistory')">
              <v-list-tile-title class="pr-3">
                <v-icon left small>add</v-icon>
                Add from history
              </v-list-tile-title>
            </v-list-tile>
          </v-list>
        </v-menu>
      </v-flex>

      <v-flex>
        <!-- <v-btn outline @click="add('uploadImageSet')">
          upload image set <v-icon class="ml-2" small>create</v-icon>
        </v-btn> -->

        <!-- <v-btn depressed @click="add('imageSet')">
          image set <v-icon class="ml-2" :size="20">add</v-icon>
        </v-btn> -->

        <v-dialog v-model="newImageSet" max-width="800">
          <!-- <template v-slot:activator="{ on }">
            <v-btn flat dark color="#D9D9D9" v-on="{ on }">
              Create new
            </v-btn>
          </template> -->
          <v-card class="pa-4">
            <v-card-title class="headline">Create Image Set</v-card-title>
            <v-card-text>
              <!-- <CreateImageSet/> -->
            </v-card-text>
            <!-- <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn flat @click="newImageSet = false">
                Close
              </v-btn>
            </v-card-actions> -->
          </v-card>
        </v-dialog>

        <v-menu open-on-hover bottom offset-y>
          <template v-slot:activator="{ on }">
            <v-btn
              class="text-none"
              color="info"
              v-on="on"
            >
              Add image set
              <v-icon class="ml-2" :size="20">add</v-icon>
            </v-btn>
          </template>

          <v-list>
            <v-list-tile @click.stop="newImageSet = true">
              <v-list-tile-title class="pr-3">
                <v-icon left small>create</v-icon>
                Create new
              </v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="add('imageSet')">
              <v-list-tile-title class="pr-3">
                <v-icon left small>add</v-icon>
                Add existing
              </v-list-tile-title>
            </v-list-tile>
          </v-list>
        </v-menu>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
// import CreateImageSet from '@/components/scientist/CreateImageSet'

export default {
  // components: {
  //   CreateImageSet
  // },
  props: {
    editMode: String,
    sequences: {
      type: Array,
      default: function () {
        return []
      }
    }
  },

  watch: {
    sequences (values) {
      values.forEach((item) => {
        let type  = (item.picture_queue_id != null) ? 'imageSet' : 'instruction'
        let value = (item.picture_queue_id != null) ? item.picture_set_id : item.description

        this.events.push({
          id: this.nonce++,
          value: value,
          type: type
        })

        this.input = null

        this.$emit('added', this.events)
      })
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

    edit: [],

    newImageSet: false
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

    createNewImageSet () {
      //
    }
  }
}
</script>

<style>
.v-text-field__details {
  display: none;
}
</style>
