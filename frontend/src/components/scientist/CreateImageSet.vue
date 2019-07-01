<template>
  <div>
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

    <v-layout mt-5>
      <v-flex>
        <Uppy :imagesetid="imageSet.imageSetId" :class="imageSet.name === null || imageSet.name === '' ? 'not-interactable' : ''"/>
      </v-flex>
    </v-layout>

    <!-- <v-layout justify-end>
      <v-btn color="success" depressed @click="createImageSet">
        Create
      </v-btn>
    </v-layout> -->
  </div>
</template>

<script>
import Uppy from '@/components/scientist/Uppy'

export default {
  components: {
    Uppy
  },

  data () {
    return {
      imageSet: {
        name: null,
        description: null,
        imageSetId: null
      }
    }
  },

  methods: {
    createImageSet () {
      this.$axios.post('/imageSet', {
        title: this.imageSet.name,
        description: this.imageSet.description
      }).then((response) => {
        this.imageSet.imageSetId = response.data.id

        // if success upload images
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
