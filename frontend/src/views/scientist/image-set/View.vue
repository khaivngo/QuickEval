<template>
  <div class="mb-12">
    <v-progress-linear indeterminate v-if="loading"></v-progress-linear>

    <div v-if="!loading">
      <v-row class="mb-12 mt-12" align="center">
        <h2 class="text-h3">
          {{ imageSet.title }}
          <!-- <v-icon color="primary">mdi-pencil</v-icon> -->
          <!-- <v-btn color="primary" outlined icon text class="ml-4 mt-1" @click.stop="editTitle = true">
            <v-icon>mdi-pencil</v-icon>
            add file
          </v-btn> -->
        </h2>
        <v-dialog
          v-model="editTitle"
          width="500"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" outlined icon text class="ml-5 mt-1" v-on="on">
              <v-icon>mdi-pencil</v-icon>
              <!-- add file -->
            </v-btn>
          </template>

          <v-card>
            <v-card-title>
              <!-- <span class="headline">Edit title</span> -->
            </v-card-title>

            <v-card-text>
              <v-container class="pt-6" fluid>
                <v-text-field
                  ref="title"
                  v-model="imageSet.title"
                  label="Title"
                  outlined
                  dense
                ></v-text-field>
              </v-container>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
              <v-btn
                color="primary"
                text
                @click="editTitle = false"
              >
                Cancel
              </v-btn>

              <v-spacer></v-spacer>

              <v-btn
                :disabled="imageSet.title === '' || creating"
                :loading="creating"
                color="success"
                class="ml-3"
                @click="updateTitle"
              >
                save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-spacer></v-spacer>

        <v-menu offset-y left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              icon
              v-bind="attrs"
              v-on="on"
              :loading="deleting"
            >
              <v-icon>mdi-dots-horizontal</v-icon>
            </v-btn>
          </template>

          <v-list>
            <v-list-item @click="destroy">
              <v-list-item-title>Delete</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-row>

      <div class="mt-12">
        <v-row v-if="reproductions.length > 0">
          <h2 class="text-h4">Images</h2>

          <Uppy :imagesetid="imageSet.id" @uploaded="addImage">
            <v-btn color="primary" id="UppyModalOpenerBtn" outlined icon text class="ml-6 mt-1">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </Uppy>
        </v-row>

        <v-row v-if="reproductions.length > 0" wrap>
          <v-col
            v-for="(image, i) in reproductions" :key="i"
            xs="6" sm="6" md="3" lg="3" xl="3"
            class="pa-1"
          >
            <div style="display: flex; justify-content: flex-end;">
              <v-menu offset-y left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    icon
                    v-bind="attrs"
                    v-on="on"
                    :loading="deleting"
                  >
                    <v-icon>mdi-dots-horizontal</v-icon>
                  </v-btn>
                </template>

                <v-list>
                  <v-list-item @click="deleteImage(image.id, i)">
                    <v-list-item-title>Delete</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
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

            <h5 class="subtitle-2 text-center qe-image-name mt-2 mb-2">
              {{ image.name }}
            </h5>
          </v-col>
        </v-row>
        <div v-else>
          <v-row class="mt-12 mb-3">
            <h2 class="text-h4">Images</h2>
          </v-row>

          <v-row>
            <Uppy :imagesetid="imageSet.id" @uploaded="addImage" style="width: 100%;">
              <div id="UppyModalOpenerBtn" class="default">
                <v-icon color="primary" large>mdi-plus</v-icon>
              </div>
            </Uppy>
          </v-row>
        </div>
      </div>

      <div class="mt-12 pt-12">
        <v-row>
          <h3 class="text-h4" v-if="original.length > 0">Reference image</h3>
        </v-row>

        <v-row v-if="original.length > 0">
          <v-col
            xs="6" sm="6" md="3" lg="3" xl="3"
            class="pa-1"
          >
            <!-- <v-icon @click="deleteImage(original[0].id)">
              mdi-dots-horizontal
            </v-icon> -->
            <div style="display: flex; justify-content: flex-end;">
              <v-menu offset-y left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    icon
                    v-bind="attrs"
                    v-on="on"
                  >
                    <v-icon>mdi-dots-horizontal</v-icon>
                  </v-btn>
                </template>

                <v-list>
                  <v-list-item @click="deleteImage(original[0].id)">
                    <v-list-item-title>Delete</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>

            <v-img
              :src="$UPLOADS_FOLDER + original[0].path"
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
            <h5 class="subtitle-2 text-center qe-image-name mt-2 mb-2">
              {{ original[0].name }}
            </h5>
          </v-col>
        </v-row>
        <div v-else class="ma-0 pa-0">
          <v-row mb-3>
            <h2 class="text-h4 mb-3">
              Reference image
              <span class="body-1">(optional)</span>
            </h2>
          </v-row>

          <v-row class="mb-3">
            <p>
              <!-- Upload the original, uncompressed, image of the image set.<br> -->
              During experiment creation you'll be able to select whether or not this image should be shown to the observer.
            </p>
          </v-row>

          <v-row class="mb-5">
            <UppyOriginal :imagesetid="imageSet.id" @uploaded="add" :width="300" :height="200"/>
          </v-row>
        </div>
      </div>
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
      imageSetId: null,
      imageSet: {},
      original: [],
      reproductions: [],
      creating: false,
      loading: false,
      deleting: false,
      editTitle: false
    }
  },

  created () {
    this.imageSetId = this.$route.params.id
    this.getImageSet()
  },

  methods: {
    add (files) {
      this.original.push(files[0])
    },

    addImage (files) {
      this.reproductions.push(files[0])
    },

    removeImage () {
      //
    },

    deleteImage (id, index) {
      // this.deleting = true
      if (confirm('Delete image?')) {
        this.$axios.delete(`/picture/${id}`).then((response) => {
          // this.imageSet.splice(index, 1)
          EventBus.$emit('success', 'Image has been deleted successfully')
          this.deleting = false
        }).catch(() => {
          this.deleting = false
        })
      }
    },

    getImageSet () {
      this.loading = true
      return this.$axios.get(`/picture-set/${this.imageSetId}`).then((response) => {
        this.imageSet = response.data

        this.original = this.imageSet.pictures.filter(image => image.is_original === 1)
        this.reproductions = this.imageSet.pictures.filter(image => image.is_original === 0)

        this.loading = false
      })
    },

    updateTitle () {
      this.creating = true

      this.$axios.patch(`/picture-set/${this.imageSetId}`, { title: this.imageSet.title }).then((response) => {
        this.creating = false
        this.editTitle = false
      }).catch(() => {
        this.creating = false
      })
    },

    destroy () {
      if (confirm('Delete image set?')) {
        this.$axios.delete(`/picture-set/${this.imageSet.id}`).then((response) => {
          if (response.data === 'deleted_picture_set') {
            // this.imageSets.splice(arrayIndex, 1)

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
  .v-input .v-input__control .v-input__slot .v-text-field__slot input {
    max-height: 200px !important;
  }
  /*.v-text-field .v-input__control .v-input__slot {
    max-height: 200px !important;
    display: flex !important;
    align-items: center !important;
  }*/
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