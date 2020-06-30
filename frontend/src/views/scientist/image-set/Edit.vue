<template>
  <div class="mb-12">
    <!-- <v-layout mb-5 mt-5>
      <h2 class="text-h4">
        Create Image Set
      </h2>
    </v-layout> -->

    <v-row>
      <v-text-field
        v-model="imageSet.name"
        label="Title"
        outlined
      ></v-text-field>

      <v-btn
        :disabled="imageSet.name === '' || creating"
        :loading="creating"
        color="success"
        class="ml-3 mt-3"
        @click="createImageSet"
      >
        Create
      </v-btn>
    </v-row>

    <div class="pa-5">
      <v-row mb-3>
        <h2 class="text-h6 mb-3">
          Original/reference image
          <span class="body-1">(optional)</span>
        </h2>
      </v-row>

      <v-row class="mb-3">
        <p>
          Upload the original, uncompressed, image of the image set.<br>
          During experiment creation you'll be able to select whether or not this image should be shown to the observer.
        </p>
      </v-row>

      <v-row class="mb-5">
        <UppyOriginal :imagesetid="imageSet.imageSetId" :width="300" :height="200"/>
      </v-row>

      <v-row class="mt-12 mb-3">
        <h2 class="text-h6">Reproduction images</h2>
      </v-row>

      <v-row>
        <Uppy :imagesetid="imageSet.imageSetId" style="width: 100%;"/>
      </v-row>
    </div>
  </div>
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

        EventBus.$emit('image-set-created', response.data)

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
