<template>
  <v-container fluid ma-0 pa-0 mb-5>
    <h2 class="headline mt-5 mb-4">Your image sets</h2>

    <p v-if="imageSets.length === 0">
      You have no image sets yet.
    </p>

    <v-layout wrap>
      <v-flex v-for="(set, i) in imageSets" :key="i" xs12 sm6 md4 lg4 xl4 pa-2 ma-1 style="border: 1px solid #ddd;">
        <!-- <v-img
          contain
          src="https://images.unsplash.com/photo-1534231284628-c3bbbdfbc6ef?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1489&q=80"
        ></v-img> -->
        <div>
          <v-icon @click="deleteImageSet(set.id, i)">delete</v-icon>
        </div>
        <router-link :to="'set/' + set.id">
          <h4 class="body-2">{{ set.title }}</h4>
        </router-link>
      </v-flex>
    </v-layout>

    <h3 class="headline mt-5 mb-4">Create new set</h3>

    <v-layout>
      <v-text-field
        v-model="imageSet.name"
        label="Name"
      ></v-text-field>
    </v-layout>

    <v-layout mt-2>
      <v-text-field
        v-model="imageSet.description"
        label="Description"
      ></v-text-field>
    </v-layout>

    <v-layout justify-end>
      <v-btn color="success" depressed @click="createImageSet" :loading="creating">
        Create
      </v-btn>
    </v-layout>

    <div :class="imageSet.imageSetId === null ? 'not-interactable' : ''">
      <v-layout mb-3 justify-center>
        <h2 class="title">Original/reference image</h2>
      </v-layout>
      <v-layout mb-5 justify-center>
        <UppyOriginal :imagesetid="imageSet.imageSetId" :width="300" :height="200"/>
      </v-layout>
      <v-layout mb-3 justify-center>
        <h2 class="title">Reproduction images</h2>
      </v-layout>
      <div justify-center>
        <Uppy :imagesetid="imageSet.imageSetId"/>
      </div>
    </div>
  </v-container>
</template>

<script>
import UppyOriginal from '@/components/scientist/UppyOriginal'
import Uppy from '@/components/scientist/Uppy'
import EventBus from '@/eventBus'

export default {
  components: {
    UppyOriginal,
    Uppy
  },

  data () {
    return {
      imageSet: {
        name: null,
        description: null,
        imageSetId: null
      },

      imageSets: [],

      creating: false
    }
  },

  created () {
    this.$axios.get('/picture-sets').then((response) => {
      this.imageSets = response.data

      // this.creating = false
    })
  },

  methods: {
    createImageSet () {
      this.creating = true

      const data = {
        title: this.imageSet.name,
        description: this.imageSet.description
      }

      this.$axios.post('/imageSet', data).then((response) => {
        this.imageSet.imageSetId = response.data.id

        this.creating = false
      })
    },

    deleteImageSet (id, arrayIndex) {
      if (confirm('Delete image set?')) {
        this.$axios.delete(`/picture-set/${id}`).then((response) => {
          console.log(response)
          if (response.data === 'deleted_picture_set') {
            this.experiments.splice(arrayIndex, 1)

            EventBus.$emit('success', 'Image set has been deleted successfully')
          } else {
            EventBus.$emit('error', 'Could not delete image set')
          }
        })
      }
    }
  }
}
</script>

<style scoped lang="css">
  .not-interactable {
    pointer-events: none;
    opacity: 0.3;
  }
</style>
