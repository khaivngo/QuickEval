<template>
  <v-card>
    <v-card-title class="justify-center pt-5 pb-0">
      <h2 class="mb-4">Image Set</h2>
    </v-card-title>

    <v-layout pr-5 pl-5 pb-5 pt-2 wrap>
      <v-flex
        v-for="(image, i) in images" :key="i"
        xs6 sm3 md3 lg3 xl3
        pa-1
      >
        <div>
          <v-icon @click="deleteImage(image.id)">delete</v-icon>
        </div>
        <v-img :src="$UPLOADS_FOLDER + image.path"></v-img>
        <h5 class="subheading qe-image-name mt-2 mb-2">
          {{ image.name }}
        </h5>
      </v-flex>
    </v-layout>
  </v-card>
</template>

<script>
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
    deleteImage (id) {
      if (confirm('Delete image?')) {
        this.$axios.delete(`/picture/${id}`).then((response) => {
          console.log(response)
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
