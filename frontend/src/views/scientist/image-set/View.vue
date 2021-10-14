<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1">
    <v-progress-linear indeterminate v-if="loading"></v-progress-linear>

    <div v-if="!loading" class="pl-3 pr-3">
      <v-row class="mb-12 mt-6" align="center">
        <h2 class="text-h3">
          {{ imageSet.title }}
        </h2>

        <v-dialog
          v-model="editTitle"
          persistent
          width="500"
        >
          <template v-slot:activator="{ on }">
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
              <v-list-item-icon class="mr-4">
                <v-icon>mdi-delete</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="pr-6">Delete</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-row>

      <div class="mt-12 pt-6">
        <v-row align="center">
          <v-col cols="auto" class="pa-0 ma-0">
            <h2 class="text-h6 font-weight-bold">
              Stimuli
            </h2>
          </v-col>

          <v-col
            class="pa-0 ma-0"
            :class="reproductions.length === 0 ? 'pt-4' : ''"
            :cols="reproductions.length === 0 ? 12 : 'auto'"
          >
            <Uppy :imagesetid="imageSet.id" @uploaded="addImage">
              <v-btn v-show="reproductions.length > 0" color="primary" id="UppyModalOpenerBtn" outlined icon text class="ml-6 mt-1">
                <v-icon>mdi-plus</v-icon>
              </v-btn>
              <div v-show="reproductions.length === 0" id="UppyModalOpenerBtn" class="default">
                <v-icon color="primary" large>mdi-plus</v-icon>
              </div>
            </Uppy>
          </v-col>
        </v-row>

        <v-row wrap>
          <v-col
            v-for="(image, i) in reproductions" :key="i"
            xs="6" sm="6" md="3" lg="3" xl="3"
            class="pa-1"
          >
            <div style="display: flex; justify-content: flex-end;">
              <ActionMenu :image="image" :index="i" @deleted="deleteImage"/>
            </div>

            <v-img
              v-if="isImage(image.extension)"
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
            <video
              v-if="isVideo(image.extension)"
              loop controls
              style="width: 100%;"
              class="video-player"
            >
              <!-- <source :src="image.path" :type="'video/'+leftExtension"> -->
              <source :src="$UPLOADS_FOLDER + image.path" :type="`video/${image.extension}`">
              Your browser does not support the video tag.
            </video>

            <h5 class="subtitle-2 text-center qe-image-name mt-2 mb-2">
              {{ image.name }}
            </h5>
          </v-col>
        </v-row>
      </div>

      <div class="mt-12 pt-12">
        <v-row v-if="original.length > 0">
          <v-col cols="12" class="ml-0 pl-0">
            <h3 class="text-h6 font-weight-bold">
              Reference/original stimulus
            </h3>
          </v-col>
          <v-col
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
                  >
                    <v-icon>mdi-dots-horizontal</v-icon>
                  </v-btn>
                </template>

                <v-list>
                  <v-list-item @click="deleteOriginal(original[0].id)">
                    <v-list-item-icon class="mr-4">
                      <v-icon>mdi-delete</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title class="pr-5">Delete</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>

            <v-img
              v-if="isImage(original[0].extension)"
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
            <video
              v-if="isVideo(original[0].extension)"
              loop controls
              style="width: 100%;"
              class="video-player"
            >
              <!-- <source :src="image.path" :type="'video/'+leftExtension"> -->
              <source :src="$UPLOADS_FOLDER + original[0].path" :type="`video/${original[0].extension}`">
              Your browser does not support the video tag.
            </video>

            <h5 class="subtitle-2 text-center qe-image-name mt-2 mb-2">
              {{ original[0].name }}
            </h5>
          </v-col>
        </v-row>

        <div v-if="original.length === 0" class="ma-0 pa-0">
          <v-row align="center" class="pb-0 mb-0">
            <v-col class="pl-0 pb-0 ml-0">
              <h2 class="text-h6 mb-2 ml-0 pl-0 font-weight-bold">
                Reference/original stimulus
                <span class="body-1">(optional)</span>
              </h2>
            </v-col>
          </v-row>

          <v-row class="mb-6 pt-0 mt-0" align="center">
            <p class="ma-0 pt-0 body-2">
              Upload the original, uncompressed, stimulus of the stimuli group.
            </p>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn icon v-on="on">
                  <v-icon color="grey lighten-1">mdi-help-circle-outline</v-icon>
                </v-btn>
              </template>
              <div class="pl-2 pr-2 pt-3 pb-3 pb-5 body-1">
                During experiment creation you'll be able to select whether or not
                this image should be shown to the observer.
              </div>
            </v-tooltip>
          </v-row>
        </div>

        <v-row class="mb-5">
          <UppyOriginal
            :imagesetid="imageSet.id"
            @uploaded="addOriginal"
            :width="300"
            :height="200"
          >
            <div v-show="original.length === 0" id="UppyModalOpenerBtnOriginal">
              <v-icon color="primary" large>mdi-plus</v-icon>
            </div>
          </UppyOriginal>
        </v-row>
      </div>
    </div>
  </div>
</template>

<script>
import UppyOriginal from '@/components/scientist/UppyOriginal'
import Uppy from '@/components/scientist/Uppy'
import ActionMenu from '@/components/scientist/ActionMenu'
import EventBus from '@/eventBus'
import mixin from '@/mixins/FileFormats.js'

export default {
  components: {
    UppyOriginal,
    Uppy,
    ActionMenu
  },

  mixins: [mixin],

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
    addOriginal (files) {
      this.original.unshift(files[0])
    },

    addImage (files) {
      this.reproductions.push(files[0])
    },

    deleteImage (index) {
      this.reproductions.splice(index, 1)
      EventBus.$emit('success', 'Image has been deleted successfully')
    },

    deleteOriginal (id) {
      // this.deleting = true
      if (confirm('Delete image?')) {
        this.$axios.delete(`/picture/${id}`).then((response) => {
          this.original.splice(0, 1)
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

        if (this.imageSet.title === 'Untitled stimuli group') {
          this.editTitle = true
        }

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
        EventBus.$emit('image-set-title', response.data)
      }).catch(() => {
        this.creating = false
      })
    },

    destroy () {
      if (confirm('Delete stimuli group?')) {
        this.$axios.delete(`/picture-set/${this.imageSet.id}`).then((response) => {
          if (response.data) {
            EventBus.$emit('image-set-deleted', response.data)
            EventBus.$emit('success', 'Stimuli group has been deleted successfully')
            this.$router.push('/scientist/image-sets')
          } else {
            EventBus.$emit('error', 'Could not delete stimuli group')
          }
        })
      }
    }
  }
}
</script>

<style scoped lang="css">
  .qe-image-name {
    word-wrap: break-word;
  }
</style>
