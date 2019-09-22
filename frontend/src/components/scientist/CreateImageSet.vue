<template>
  <v-container fluid
    pb-0 pl-0 pr-0 pt-5
    ma-0 mb-5
  >
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
          <v-icon @click="deleteImageSet(set.id, i)">
            delete
          </v-icon>
        </div>
        <router-link :to="`set/${set.id}`">
          <h4 class="body-2">
            {{ set.title }}
          </h4>
        </router-link>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import EventBus from '@/eventBus'

export default {
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
