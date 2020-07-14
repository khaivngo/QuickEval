<template>
  <v-row class="fill-height" no-gutters>
    <v-col class="fill-height" style="background: #ddd; max-width: 256px;">
      <v-navigation-drawer permanent app style="z-index: 1; padding-top: 64px; margin-left: 256px; max-width: 300px;">
        <v-list-item class="mt-2">
          <v-list-item-content>
            <v-list-item-title>
              <v-btn
                :loading="creating"
                class="pl-2"
                text
                @click="create"
              >
                <v-icon color="primary" class="pa-0 ma-0">mdi-plus</v-icon>
                Create new
              </v-btn>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list
          dense
        >
          <v-list-item-group v-model="active" color="primary">
            <v-list-item
              v-for="(imageSet, i) in imageSets"
              :key="i"
              link
              @click="$router.push(`/scientist/image-sets/view/${imageSet.id}`)"
              class="pl-8"
            >
              <v-list-item-content>
                <v-list-item-title>
                  {{ imageSet.title }}
                </v-list-item-title>
                <!-- <v-list-item-subtitle>{{ experiment.completed_results_count }} completed</v-list-item-subtitle> -->
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>

        <v-progress-linear v-slot:progress indeterminate class="ma-0" :height="2" v-if="loading"></v-progress-linear>
        <div class="caption ma-4" v-if="loading === false && imageSets.length === 0">
          You have no image sets. Yet...
        </div>
      </v-navigation-drawer>
    </v-col>

    <v-col class="pr-12 pl-12 pt-6">
      <router-view :key="$route.params.id || ''"/>
    </v-col>
  </v-row>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      loading: false,
      imageSets: [],
      active: null,

      valid: false,
      rules: {
        required: value => !!value || 'Required.'
      },

      creating: false,
      newImageSet: {
        name: '',
        description: ''
      },

      pagination: {
        sortBy: 'name'
      }
    }
  },

  created () {
    this.loading = true
    this.$axios.get('/picture-sets').then((response) => {
      this.imageSets = response.data

      let activeIndex = this.imageSets.findIndex(set => set.id === parseInt(this.$route.params.id))
      this.active = activeIndex

      this.loading = false
    }).catch(error => {
      this.loading = false
      alert(error)
    })

    EventBus.$on('image-set-created', (payload) => {
      this.imageSets.unshift(payload)
    })

    EventBus.$on('image-set-deleted', (payload) => {
      let exp = this.imageSets.findIndex(exp => exp.id === payload.id)
      this.imageSets.splice(exp, 1)

      this.active = null
    })
  },

  methods: {
    create () {
      this.creating = true

      const data = {
        title: 'Untitled image set',
        description: ' '
      }

      this.$axios.post('/imageSet', data).then((response) => {
        this.imageSets.unshift(response.data)
        // set active first item in imageSets array
        this.active = 0
        // and redirect
        this.$router.push(`/scientist/image-sets/view/${this.imageSets[0].id}`)
        // '/scientist/image-sets/edit'

        this.creating = false
      }).catch(error => {
        this.creating = false
        alert(error)
      })
    },

    destroy (id, arrayIndex) {
      if (confirm('Delete image set?')) {
        this.$axios.delete(`/picture-set/${id}`).then((response) => {
          if (response.data === 'deleted_picture_set') {
            this.imageSets.splice(arrayIndex, 1)

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
