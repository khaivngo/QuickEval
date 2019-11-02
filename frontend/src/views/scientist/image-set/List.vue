<template>
  <div>
    <v-layout mb-5 mt-5>
      <h2 class="display-1">
        Your Image Sets
      </h2>
    </v-layout>

    <v-layout justify-end mb-3>
      <v-layout align-center>
        <v-text-field
          v-model="newImageSet.name"
          label="Title"
        ></v-text-field>

        <v-btn
          :disabled="newImageSet.name === '' || creating === true"
          :loading="creating"
          color="success" class="ma-0 ml-3"
          @click="createNew"
        >
          Create New
        </v-btn>

        <!-- <v-text-field
          v-model="newImageSet.description"
          label="Description"
        ></v-text-field> -->
      </v-layout>
    </v-layout>

    <v-data-table
      :headers="[
        { text: 'Title', value: 'name', align: 'left' },
        // { text: 'Visible to the public', value: 'public' },
        { text: 'Actions', value: 'edit', align: 'right', sortable: false }
      ]"
      :items="imageSets"
      no-data-text=""
      item-key="id"
      class="elevation-1"
      hide-actions
      :loading="loading"
    >
      <!-- <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear> -->
      <template v-slot:no-data>
        <div class="caption text-xs-center" v-if="loading === false">
          You have no image sets.
        </div>
      </template>
      <template v-slot:items="props">
        <td>
          <router-link :to="`/scientist/image-sets/view/${props.item.id}`" class="qe-title-link">
            {{ props.item.title }}
          </router-link>
        </td>
        <td align="right">
          <!-- <v-btn :to="'/scientist/experiment/edit/' + props.item.id" flat icon>
            <v-icon>edit</v-icon>
          </v-btn> -->

          <v-btn flat icon @click="destroy(props.item.id, props.index)">
            <v-icon>delete</v-icon>
          </v-btn>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      loading: false,
      imageSets: [],

      creating: false,
      newImageSet: {
        name: '',
        description: ''
      }
    }
  },

  created () {
    this.loading = true
    this.$axios.get('/picture-sets').then((response) => {
      this.imageSets = response.data
      this.loading = false
    }).catch(error => {
      this.loading = false
      alert(error)
    })
  },

  methods: {
    createNew () {
      this.creating = true

      const data = {
        title: this.newImageSet.name,
        description: ' '
      }

      this.$axios.post('/imageSet', data).then((response) => {
        this.imageSets.unshift(response.data)
        this.newImageSet.name = ''
        this.creating = false
        this.$router.push(`/scientist/image-sets/${response.data.id}/file-upload`)
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
