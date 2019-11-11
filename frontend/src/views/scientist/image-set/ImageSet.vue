<template>
  <div>
    <v-layout mb-5 mt-5>
      <h2 class="display-1">
        Image Set
      </h2>
    </v-layout>

    <v-card>
      <Back>Back to all image sets</Back>

      <v-layout justify-end pr-4 pt-2 pb-2>
        <!-- <v-text-field value="wwww"></v-text-field> -->

        <v-btn class="success" :to="`/scientist/image-sets/${imageSetId}/file-upload`">
          Add Files
        </v-btn>
      </v-layout>

      <v-layout pl-4 pr-4 pb-4 wrap>
        <v-flex
          v-for="(image, i) in images" :key="i"
          xs6 sm3 md3 lg3 xl3
          pa-1
        >
          <div class="qe-actions">
            <h3 class="title font-weight-regular ml-1 pt-1 pb-1">
              <template v-if="image.is_original === 1">Original</template>
              <template v-else>&nbsp;</template>
            </h3>

            <v-icon @click="deleteImage(image.id, i)">
              delete
            </v-icon>
          </div>

          <v-img
            :src="$UPLOADS_FOLDER + image.path"
            aspect-ratio="1"
            class="grey lighten-2"
          >
            <template v-slot:placeholder>
              <v-layout
                fill-height
                align-center
                justify-center
                ma-0
              >
                <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
              </v-layout>
            </template>
          </v-img>

          <h5 class="subheading qe-image-name mt-2 mb-2">
            {{ image.name }}
          </h5>
        </v-flex>

        <!-- <v-container grid-list-sm fluid>
          <v-layout row wrap>
            <v-flex
              v-for="(image, i) in images"
              :key="i"
              xs4
              d-flex
            >
              <v-card flat tile class="d-flex">
                <v-img
                  :src="$UPLOADS_FOLDER + image.path"
                  aspect-ratio="1"
                  class="grey lighten-2"
                >
                  <template v-slot:placeholder>
                    <v-layout
                      fill-height
                      align-center
                      justify-center
                      ma-0
                    >
                      <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                    </v-layout>
                  </template>
                </v-img>
              </v-card>
            </v-flex>
          </v-layout>
        </v-container> -->

      </v-layout>
    </v-card>
  </div>
</template>

<script>
import EventBus from '@/eventBus'
import Back from '@/components/Back'

export default {
  components: {
    Back
  },

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

  .qe-actions {
    /*border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-top: 1px solid #ddd;*/
    display: flex;
    background-color: #efefef;
    padding: 4px;
    justify-content: space-between;
  }
</style>
