<template>
  <div class="mb-12">
    <v-layout mb-5 mt-5>
      <h2 class="text-h4">
        Add Image Files
      </h2>
    </v-layout>

    <!-- <v-card> -->
      <!-- <v-layout mt-2>
        <v-text-field
          v-model="imageSet.description"
          label="Description"
        ></v-text-field>
      </v-layout> -->

      <div class="pa-5">
        <v-row mb-3>
          <h2 class="title">
            Original/reference image
            <span class="subheading">(optional)</span>
          </h2>
        </v-row>

        <v-row>
          <p>
            Upload the original, uncompressed, image of the image set.<br>
            During experiment creation you'll be able to select whether or not this image should be shown to the observer.
          </p>
        </v-row>

        <v-row class="mb-5">
          <UppyOriginal :imagesetid="imageSet.imageSetId" :width="300" :height="200"/>
        </v-row>

        <v-row class="mt-12 mb-3">
          <h2 class="title">Reproduction images</h2>
        </v-row>

        <v-row>
          <Uppy :imagesetid="imageSet.imageSetId" style="width: 100%;"/>
        </v-row>
      </div>
    <!-- </v-card> -->
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

  created () {
    this.imageSet.imageSetId = this.$route.params.id
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
