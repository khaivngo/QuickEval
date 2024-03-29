<template>
  <div class="d-flex flex-grow-1">
    <div class="qe-nav-drawer">
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

      <v-list-item v-if="loading === false && imageSets.length === 0">
        <v-list-item-content>
          <v-list-item-title>
            <div class="caption ma-4">
              You have no image sets. Yet...
            </div>
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

      <v-progress-linear
        v-if="loading"
        indeterminate
        class="ma-0"
        :height="2"
      ></v-progress-linear>
    </div>

    <!-- the menu above is position fixed, so we put a "mold" below -->
    <div style="flex: 0 0 270px; height: 20px;"></div>

    <router-view :key="$route.params.id || ''"/>
  </div>
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
    this.$axios.get('/picture-set').then((response) => {
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

    EventBus.$on('image-set-title', (payload) => {
      console.log(payload)
      let id = this.imageSets.findIndex(exp => exp.id === payload.id)
      this.imageSets[id].title = payload.title
    })
  },

  methods: {
    create () {
      this.creating = true

      const data = {
        title: 'Untitled stimuli group',
        description: ' '
      }

      this.$axios.post('/picture-set', data).then((response) => {
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
      if (confirm('Delete stimuli group?')) {
        this.$axios.delete(`/picture-set/${id}`).then((response) => {
          if (response.data === 'deleted_picture_set') {
            this.imageSets.splice(arrayIndex, 1)

            EventBus.$emit('success', 'Stimuli group has been deleted successfully')
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
  .not-interactable {
    pointer-events: none;
    opacity: 0.3;
  }
  .qe-nav-drawer {
    z-index: 1;
    padding-top: 64px;
    width: 270px;
    overflow-y: auto;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 240px;
    border-right: 1px solid #ddd;
    background: #fff;
  }
  @media (max-width: 1150px) {
    .qe-nav-drawer {
      left: 58px;
    }
  }
</style>
