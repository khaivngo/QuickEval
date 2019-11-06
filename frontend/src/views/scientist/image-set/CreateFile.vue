<template>
  <div>
    <v-layout mb-5 mt-5>
      <h2 class="display-1">
        Add Image Files
      </h2>
    </v-layout>

    <v-card>
      <Back>Back to image set</Back>

      <!-- <v-layout mt-2>
        <v-text-field
          v-model="imageSet.description"
          label="Description"
        ></v-text-field>
      </v-layout> -->

      <!-- <v-layout justify-end>
        <v-btn color="success" depressed @click="createImageSet" :loading="creating">
          Create
        </v-btn>
      </v-layout> -->

      <div class="pa-5">
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
          <UppyOriginal :imagesetid="imageSet.imageSetId" :width="300" :height="200"/>
        </v-layout>

        <v-layout mb-3>
          <h2 class="title">Reproduction images</h2>
        </v-layout>

        <div>
          <Uppy :imagesetid="imageSet.imageSetId"/>
        </div>

        <v-layout mt-5 justify-end>
          <v-btn class="success" @click="$router.back()">
            Done
          </v-btn>
        </v-layout>
      </div>
    </v-card>
  </div>
</template>

<script>
import UppyOriginal from '@/components/scientist/UppyOriginal'
import Uppy from '@/components/scientist/Uppy'
import Back from '@/components/Back'

export default {
  components: {
    UppyOriginal,
    Uppy,
    Back
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
