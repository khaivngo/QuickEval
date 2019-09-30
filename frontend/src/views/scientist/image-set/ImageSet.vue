<template>
  <div>
    <v-layout mb-5>
      <h2 class="display-1">
        Image Set
      </h2>
    </v-layout>

    <v-card>
      <v-container class="ma-0 pa-2" style="border-bottom: 1px solid #ccc;">
        <v-layout align-center justify-space-between>
          <v-btn outline fab small @click="$router.back()">
            <v-icon>arrow_back</v-icon>
          </v-btn>

          <v-btn class="success" :to="`/scientist/image-sets/${imageSetId}/file-upload`">
            Add Files
          </v-btn>
        </v-layout>
      </v-container>
      <v-layout pa-5 wrap>
        <v-flex
          v-for="(image, i) in images" :key="i"
          xs6 sm3 md3 lg3 xl3
          pa-1
        >
          <h3 class="title">
            <template v-if="image.is_original === 1">Original</template>
            <template v-else>&nbsp;</template>
          </h3>
          <div>
            <v-icon @click="deleteImage(image.id, i)">
              delete
            </v-icon>
          </div>
          <v-img :src="$UPLOADS_FOLDER + image.path"></v-img>
          <h5 class="subheading qe-image-name mt-2 mb-2">
            {{ image.name }}
          </h5>
        </v-flex>
      </v-layout>
    </v-card>
  </div>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      imageSetId: null,
      images: [],

      creating: false
    }
  },

  created () {
    this.imageSetId = this.$route.params.id
    this.getImages()
  },

  methods: {
    deleteImage (id, index) {
      if (confirm('Delete image?')) {
        this.$axios.delete(`/picture/${id}`).then((response) => {
          this.images.splice(index, 1)
          EventBus.$emit('success', 'Image has been deleted successfully')
        })
      }
    },

    getImages () {
      return this.$axios.get(`/picture-set/images/${this.imageSetId}`).then((response) => {
        this.images = response.data
      })
    }
  }
}
</script>

<style scoped lang="css">
  .qe-image-name {
    word-wrap: break-word;
  }
</style>
