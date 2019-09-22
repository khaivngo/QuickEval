<template>
  <div>
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
        <h2 class="title">Original/reference image (optional)</h2>
      </v-layout>
      <v-layout justify-center class="text-xs-center">
        <p>
          Upload the original, uncompressed, image of the image set. You'll be able to later select whether this image should
          be shown in the experiment.
        </p>
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
  </div>
</template>

<script>
import UppyOriginal from '@/components/scientist/UppyOriginal'
import Uppy from '@/components/scientist/Uppy'

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
